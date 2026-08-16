<?php

namespace Tests\Feature;

use App\Models\AccommodationType;
use App\Models\Booking;
use App\Models\ConservationArea;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Proves the locking primitive AvailabilityService relies on (a raw no-op
 * UPDATE used as a mutex on the parent resource row) actually serializes two
 * concurrent transactions, using two genuinely independent PDO connections
 * against a shared file-backed SQLite database. This cannot be done against
 * the default :memory: test connection, which can't be shared across
 * connections, and PHP is single-threaded so this is the only way to get
 * real second-connection lock contention without spawning OS processes.
 *
 * This deliberately does NOT use RefreshDatabase / the app's default test
 * connection -- it manages its own temp SQLite file and two connections
 * pointed at it, torn down at the end of each test.
 */
class BookingConcurrencyTest extends TestCase
{
    private string $dbFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dbFile = sys_get_temp_dir() . '/booking_concurrency_' . uniqid() . '.sqlite';
        touch($this->dbFile);

        foreach (['conc_a', 'conc_b'] as $name) {
            config(["database.connections.{$name}" => [
                'driver' => 'sqlite',
                'database' => $this->dbFile,
                'prefix' => '',
                'foreign_key_constraints' => true,
                'busy_timeout' => null, // matches production default: fail fast, don't wait
            ]]);
        }

        Artisan::call('migrate', ['--database' => 'conc_a', '--force' => true]);
    }

    protected function tearDown(): void
    {
        DB::purge('conc_a');
        DB::purge('conc_b');
        @unlink($this->dbFile);

        parent::tearDown();
    }

    public function test_two_connections_racing_for_the_last_unit_only_one_wins(): void
    {
        $area = ConservationArea::on('conc_a')->create(ConservationArea::factory()->make()->toArray());
        $accommodation = AccommodationType::on('conc_a')->create(AccommodationType::factory()->make([
            'conservation_area_id' => $area->id,
            'total_units' => 1,
        ])->toArray());

        // Connection A begins a transaction and acquires the mutex lock on
        // the accommodation_types row -- exactly what
        // AvailabilityService::lockResources() does.
        DB::connection('conc_a')->beginTransaction();
        DB::connection('conc_a')->statement('UPDATE accommodation_types SET id = id WHERE id = ?', [$accommodation->id]);

        // Connection B tries to acquire the same lock while A still holds
        // it (A hasn't committed). With busy_timeout unset, SQLite must
        // fail this immediately rather than silently letting both through.
        DB::connection('conc_b')->beginTransaction();
        $bWasBlocked = false;
        try {
            DB::connection('conc_b')->statement('UPDATE accommodation_types SET id = id WHERE id = ?', [$accommodation->id]);
        } catch (\Throwable $e) {
            $bWasBlocked = true;
            $this->assertStringContainsString('locked', strtolower($e->getMessage()));
        } finally {
            DB::connection('conc_b')->rollBack();
        }

        $this->assertTrue($bWasBlocked, 'Connection B should have been blocked by connection A\'s lock.');

        // Connection A, still holding the lock, sees zero existing bookings
        // and safely creates one, then commits.
        $existingCount = DB::connection('conc_a')->table('bookings')
            ->where('accommodation_type_id', $accommodation->id)
            ->count();
        $this->assertSame(0, $existingCount);

        Booking::on('conc_a')->create(Booking::factory()->make([
            'conservation_area_id' => $area->id,
            'accommodation_type_id' => $accommodation->id,
        ])->toArray());

        DB::connection('conc_a')->commit();

        $this->assertSame(1, Booking::on('conc_a')->where('accommodation_type_id', $accommodation->id)->count());
    }
}
