<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    public const ACTION_BOOKING_CREATED = 'booking.created';
    public const ACTION_BOOKING_STATUS_CHANGED = 'booking.status_changed';
    public const ACTION_BOOKING_RESCHEDULED = 'booking.rescheduled';
    public const ACTION_BOOKING_OVERRIDE = 'booking.override';
    public const ACTION_BOOKING_ASSIGNED = 'booking.assigned';
    public const ACTION_PAYMENT_UPDATED = 'payment.updated';
    public const ACTION_AVAILABILITY_BLOCKED = 'availability.blocked';
    public const ACTION_AVAILABILITY_UNBLOCKED = 'availability.unblocked';

    public const ACTIONS = [
        self::ACTION_BOOKING_CREATED,
        self::ACTION_BOOKING_STATUS_CHANGED,
        self::ACTION_BOOKING_RESCHEDULED,
        self::ACTION_BOOKING_OVERRIDE,
        self::ACTION_BOOKING_ASSIGNED,
        self::ACTION_PAYMENT_UPDATED,
        self::ACTION_AVAILABILITY_BLOCKED,
        self::ACTION_AVAILABILITY_UNBLOCKED,
    ];

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id', 'description', 'changes', 'reason', 'created_at',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Record an audit trail entry. $subject is the affected model (e.g. the
     * Booking that changed); pass null for actions with no single subject.
     * The acting user is taken from the current auth context automatically
     * -- pass null there (system actions, e.g. the expiry scheduler).
     */
    public static function record(
        string $action,
        ?Model $subject,
        string $description,
        ?array $changes = null,
        ?string $reason = null,
    ): self {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->id,
            'description' => $description,
            'changes' => $changes,
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }
}
