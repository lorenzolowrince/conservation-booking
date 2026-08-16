<?php

namespace App\Services;

final readonly class AvailabilityResult
{
    /**
     * @param  array{capacity: ?int, used: int, remaining: ?int, available: bool}|null  $package
     * @param  array{total_units: int, used_units: int, units_required: int, remaining_units: int, available: bool}|null  $accommodation
     */
    public function __construct(
        public bool $available,
        public ?string $message,
        public ?array $package = null,
        public ?array $accommodation = null,
        public bool $blocked = false,
    ) {
    }

    public function toArray(): array
    {
        return [
            'available' => $this->available,
            'message' => $this->message,
            'package' => $this->package,
            'accommodation' => $this->accommodation,
            'blocked' => $this->blocked,
        ];
    }
}
