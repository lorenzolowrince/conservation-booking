<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
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
