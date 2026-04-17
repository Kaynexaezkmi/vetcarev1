<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'rating', 'message', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Feedback::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Feedback::class, 'parent_id');
    }

    public function scopeParentFeedback($query)
    {
        return $query->whereNull('parent_id');
    }
}