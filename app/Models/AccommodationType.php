<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccommodationType extends Model
{
    protected $fillable = [
        'conservation_area_id', 'name', 'type', 'description',
        'capacity', 'price_per_night', 'price_per_night_foreigner',
        'amenities', 'image', 'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
        'price_per_night' => 'decimal:2',
        'price_per_night_foreigner' => 'decimal:2',
    ];

    public function conservationArea(): BelongsTo
    {
        return $this->belongsTo(ConservationArea::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
