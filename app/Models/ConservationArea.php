<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConservationArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'short_name', 'slug', 'description', 'about',
        'location', 'area_hectares', 'cover_image', 'gallery_images',
        'highlights', 'wildlife', 'best_time_to_visit', 'difficulty_level',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'highlights' => 'array',
        'wildlife' => 'array',
        'is_active' => 'boolean',
    ];

    public function accommodationTypes(): HasMany
    {
        return $this->hasMany(AccommodationType::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class)->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
