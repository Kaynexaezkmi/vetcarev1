<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'type',
        'send_at',
        'is_sent',
        'sent_at',
        'read_at',
    ];

    protected $casts = [
        'send_at' => 'datetime',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function scopePending($query)
    {
        return $query->where('is_sent', false)
            ->where('send_at', '<=', now());
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->update(['read_at' => now()]);
        }
    }
}
