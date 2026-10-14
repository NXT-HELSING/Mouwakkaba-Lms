<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'question', 'type', 'explanation',
        'points', 'order', 'image',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
        'type' => 'string',
    ];

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(QuizResponse::class);
    }

    // Accessors
    public function getCorrectAnswersAttribute()
    {
        return $this->answers()->where('is_correct', true)->get();
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    // Scopes
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}