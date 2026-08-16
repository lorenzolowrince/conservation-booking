<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    /** Statuses that consume package/accommodation inventory. */
    public const ACTIVE_STATUSES = ['pending', 'confirmed'];

    protected $fillable = [
        'booking_ref', 'user_id', 'conservation_area_id', 'package_id',
        'accommodation_type_id', 'contact_name', 'contact_email',
        'contact_phone', 'contact_nationality', 'booking_type',
        'check_in_date', 'check_out_date', 'num_adults', 'num_children',
        'subtotal', 'tax', 'total_amount', 'status', 'payment_status',
        'payment_method', 'special_requests', 'admin_notes',
        'confirmed_at', 'cancelled_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conservationArea(): BelongsTo
    {
        return $this->belongsTo(ConservationArea::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function accommodationType(): BelongsTo
    {
        return $this->belongsTo(AccommodationType::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(BookingGuest::class);
    }

    /**
     * Bookings whose stay overlaps [$checkIn, $checkOut) and whose status
     * still consumes inventory. Half-open interval: a checkout on the same
     * day as another booking's check-in does NOT count as a conflict.
     */
    public function scopeActiveOverlap(Builder $query, $checkIn, $checkOut): Builder
    {
        // whereDate() (not where()) because check_in_date/check_out_date are
        // stored as full datetimes ("2026-08-15 00:00:00") even though the
        // 'date' cast is used -- a plain string comparison against a bare
        // "2026-08-15" would treat the stored value as later in the day and
        // break the back-to-back-dates boundary case.
        return $query->whereIn('status', self::ACTIVE_STATUSES)
            ->whereDate('check_in_date', '<', $checkOut)
            ->whereDate('check_out_date', '>', $checkIn);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'confirmed' => 'green',
            'pending'   => 'yellow',
            'cancelled' => 'red',
            'completed' => 'blue',
            default     => 'gray',
        };
    }

    public static function generateRef(string $areaCode): string
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;
        return strtoupper($areaCode) . '-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
