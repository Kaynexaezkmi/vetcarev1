<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'appointment_id',
        'created_by',
        'submission_token',
        'title',
        'diagnosis',
        'treatment',
        'notes',
        'file_path',
        'file_type',
        'record_date',
        'next_call',
        'seen_by_user_at',
    ];

    protected $casts = [
        'record_date' => 'date',
        'seen_by_user_at' => 'datetime',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
