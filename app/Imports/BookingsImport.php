<?php

namespace App\Imports;

use App\Models\AccommodationType;
use App\Models\Booking;
use App\Models\ConservationArea;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class BookingsImport implements ToCollection, WithHeadingRow, WithCustomValueBinder
{
    public int $imported = 0;

    /** @var array<int, string> */
    public array $errors = [];

    /**
     * Force every cell to be read as a plain string. Without this, CSV
     * values that look numeric (e.g. a phone number "+60123456789" or a
     * passport number with a leading zero) get silently type-coerced to
     * int/float, corrupting the value before it ever reaches our code.
     */
    public function bindValue(Cell $cell, mixed $value): bool
    {
        $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

        return true;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // account for the header row

            if ($row->filter(fn ($value) => filled($value))->isEmpty()) {
                continue; // skip fully blank rows
            }

            try {
                $this->importRow($row, $rowNumber);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->errors[] = "Row {$rowNumber}: {$e->getMessage()}";
            }
        }
    }

    private function importRow(Collection $row, int $rowNumber): void
    {
        $data = $row->toArray();

        $validator = Validator::make($data, [
            'conservation_area' => 'required|string',
            'contact_name' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'contact_nationality' => 'required|string',
            'check_in_date' => 'required',
            'check_out_date' => 'required',
            'subtotal' => 'required|numeric',
            'num_adults' => 'nullable|numeric',
            'num_children' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'status' => 'nullable|in:pending,confirmed,cancelled,completed',
            'payment_status' => 'nullable|in:unpaid,paid,refunded',
            'booking_type' => 'nullable|in:package,accommodation_only,day_trip',
        ]);

        if ($validator->fails()) {
            throw new \RuntimeException($validator->errors()->first());
        }

        $area = $this->resolveArea($data['conservation_area']);

        $package = null;
        if (filled($data['package'] ?? null)) {
            $package = Package::where('conservation_area_id', $area->id)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($data['package']))])
                ->first();

            if (! $package) {
                throw new \RuntimeException("Package \"{$data['package']}\" not found under {$area->name}.");
            }
        }

        $accommodationType = null;
        if (filled($data['accommodation_type'] ?? null)) {
            $accommodationType = AccommodationType::where('conservation_area_id', $area->id)
                ->whereRaw('LOWER(name) = ?', [strtolower(trim($data['accommodation_type']))])
                ->first();

            if (! $accommodationType) {
                throw new \RuntimeException("Accommodation type \"{$data['accommodation_type']}\" not found under {$area->name}.");
            }
        }

        $checkIn = $this->parseDate($data['check_in_date']);
        $checkOut = $this->parseDate($data['check_out_date']);

        if ($checkOut->lte($checkIn)) {
            throw new \RuntimeException('Check-out date must be after check-in date.');
        }

        $subtotal = (float) $data['subtotal'];
        $tax = (float) ($data['tax'] ?? 0);
        $totalAmount = filled($data['total_amount'] ?? null) ? (float) $data['total_amount'] : $subtotal + $tax;

        $bookingRef = filled($data['booking_ref'] ?? null)
            ? trim($data['booking_ref'])
            : Booking::generateRef($area->code);

        if (Booking::where('booking_ref', $bookingRef)->exists()) {
            throw new \RuntimeException("Booking reference \"{$bookingRef}\" already exists.");
        }

        DB::transaction(function () use ($data, $area, $package, $accommodationType, $checkIn, $checkOut, $subtotal, $tax, $totalAmount, $bookingRef) {
            Booking::create([
                'booking_ref' => $bookingRef,
                'conservation_area_id' => $area->id,
                'package_id' => $package?->id,
                'accommodation_type_id' => $accommodationType?->id,
                'contact_name' => trim($data['contact_name']),
                'contact_email' => trim($data['contact_email']),
                'contact_phone' => trim($data['contact_phone']),
                'contact_nationality' => trim($data['contact_nationality']),
                'booking_type' => $data['booking_type'] ?? 'package',
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'num_adults' => (int) ($data['num_adults'] ?? 1) ?: 1,
                'num_children' => (int) ($data['num_children'] ?? 0),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'status' => $data['status'] ?? 'pending',
                'payment_status' => $data['payment_status'] ?? 'unpaid',
                'payment_method' => $data['payment_method'] ?? null,
                'special_requests' => $data['special_requests'] ?? null,
            ]);
        });
    }

    private function resolveArea(string $value): ConservationArea
    {
        $value = trim($value);

        $area = ConservationArea::whereRaw('LOWER(code) = ?', [strtolower($value)])
            ->orWhereRaw('LOWER(short_name) = ?', [strtolower($value)])
            ->orWhereRaw('LOWER(name) = ?', [strtolower($value)])
            ->first();

        if (! $area) {
            throw new \RuntimeException("Conservation area \"{$value}\" not recognized (use its code, e.g. DVCA).");
        }

        return $area;
    }

    private function parseDate(mixed $value): Carbon
    {
        if (is_numeric($value)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value));
        }

        try {
            return Carbon::parse((string) $value);
        } catch (\Throwable) {
            throw new \RuntimeException("Could not read date \"{$value}\" — use YYYY-MM-DD.");
        }
    }
}
