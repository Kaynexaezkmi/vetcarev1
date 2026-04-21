<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PetActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'user_id',
        'action',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
