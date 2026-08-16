<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'conservation_area_id', 'name', 'slug', 'description',
        'duration_days', 'min_pax', 'max_pax', 'daily_capacity',
        'price_per_person', 'price_per_person_foreigner',
        'inclusions', 'exclusions', 'itinerary', 'image',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'inclusions' => 'array',
        'exclusions' => 'array',
        'itinerary' => 'array',
        'is_active' => 'boolean',
        'price_per_person' => 'decimal:2',
        'price_per_person_foreigner' => 'decimal:2',
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
