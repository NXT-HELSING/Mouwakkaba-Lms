<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id', 'answer', 'is_correct', 'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'order' => 'integer',
    ];

    // Relationships
    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    public function responses()
    {
        return $this->hasMany(QuizResponse::class, 'answer_id');
    }

    // Scopes
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}