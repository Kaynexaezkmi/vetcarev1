<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_id',
        'service_id',
        'appointment_date',
        'appointment_time',
        'reason',
        'status',
        'rescheduled',
        'cancellation_reason',
        'cancelled_by',
        'notes',
        'payment_method',
        'payment_reference',
        'payment_proof_path',
        'service_amount',
        'reservation_fee',
        'payment_submitted_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'rescheduled' => 'boolean',
        'service_amount' => 'decimal:2',
        'reservation_fee' => 'decimal:2',
        'payment_submitted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Appointment $appointment) {
            $appointment->slot_guard = $appointment->shouldBlockSlot()
                ? $appointment->buildSlotGuard()
                : null;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function reminder()
    {
        return $this->hasOne(Reminder::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'cancelled' => 'Cancelled',
            'rejected' => 'Rejected',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'approved' => 'bg-green-100 text-green-700',
            'completed' => 'bg-blue-100 text-blue-700',
            'cancelled', 'rejected' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getStatusActionLabelAttribute(): string
    {
        return match ($this->status) {
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
            default => $this->statusLabel,
        };
    }

    public function canReschedule($isAdmin = false): bool
    {
        if ($isAdmin) {
            return in_array($this->status, ['pending', 'approved']) && ! $this->rescheduled;
        }

        return $this->status === 'pending' && ! $this->rescheduled;
    }

    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('appointment_date', $date);
    }

    public function scopeBookedSlots($query, $date)
    {
        return $query->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('appointment_time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'))
            ->unique()
            ->toArray();
    }

    public function shouldBlockSlot(): bool
    {
        return in_array($this->status, ['pending', 'approved'], true)
            && ! empty($this->appointment_date)
            && ! empty($this->appointment_time);
    }

    public function buildSlotGuard(): ?string
    {
        if (! $this->shouldBlockSlot()) {
            return null;
        }

        return Carbon::parse($this->appointment_date)->toDateString()
            .' '
            .Carbon::parse($this->appointment_time)->format('H:i:s');
    }
}
