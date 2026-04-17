<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function diffActivityAttributes(array $attributes): array
    {
        $labels = [
            'record_date' => 'Record Date',
            'next_call' => 'Next Call',
            'diagnosis' => 'Diagnosis',
            'treatment' => 'Treatment',
            'notes' => 'Notes',
        ];

        $changes = [];

        foreach ($labels as $key => $label) {
            $current = $this->normalizeActivityValue($key, $this->{$key});
            $updated = $this->normalizeActivityValue($key, $attributes[$key] ?? null);

            if ($current === $updated) {
                continue;
            }

            $changes[] = [
                'field' => $key,
                'label' => $label,
                'from' => $this->formatActivityValue($key, $current),
                'to' => $this->formatActivityValue($key, $updated),
            ];
        }

        return $changes;
    }

    protected function normalizeActivityValue(string $key, mixed $value): ?string
    {
        if ($value instanceof Carbon) {
            return $value->format('Y-m-d');
        }

        if ($key === 'record_date' && !empty($value)) {
            return Carbon::parse($value)->format('Y-m-d');
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        return $value === '' || $value === null ? null : (string) $value;
    }

    protected function formatActivityValue(string $key, ?string $value): string
    {
        if ($value === null) {
            return 'Not set';
        }

        if ($key === 'record_date') {
            return Carbon::parse($value)->format('M d, Y');
        }

        return $value;
    }
}
