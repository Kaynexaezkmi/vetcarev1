<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'breed',
        'gender',
        'date_of_birth',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(PetActivityLog::class)->latest();
    }

    public function getAgeAttribute()
    {
        if ($this->date_of_birth) {
            return $this->date_of_birth->age . ' years old';
        }
        return 'Unknown';
    }

    public function diffProfileAttributes(array $attributes): array
    {
        $labels = [
            'name' => 'Name',
            'gender' => 'Gender',
            'type' => 'Species',
            'breed' => 'Breed',
            'date_of_birth' => 'DOB',
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

    public function logProfileUpdate(array $changes, ?User $actor = null, string $context = 'pet profile'): void
    {
        if ($changes === []) {
            return;
        }

        $contextLabels = [
            'settings' => 'Settings',
            'medical_history' => 'Medical History',
            'admin_patient_records' => 'Admin Patient Records',
        ];

        $this->activityLogs()->create([
            'user_id' => $actor?->id,
            'action' => 'pet_profile_updated',
            'description' => ($actor?->name ?? 'System') . ' updated the pet profile via ' . ($contextLabels[$context] ?? 'Pet Profile') . '.',
            'properties' => [
                'context' => $context,
                'changes' => $changes,
            ],
        ]);
    }

    protected function normalizeActivityValue(string $key, mixed $value): ?string
    {
        if ($value instanceof Carbon) {
            return $value->format('Y-m-d');
        }

        if ($key === 'date_of_birth' && !empty($value)) {
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

        if ($key === 'date_of_birth') {
            return Carbon::parse($value)->format('M d, Y');
        }

        return $value;
    }
}
