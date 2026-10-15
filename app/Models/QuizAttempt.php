<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'user_id', 'enrollment_id', 'attempt_number',
        'score', 'max_score', 'percentage', 'passed',
        'started_at', 'submitted_at', 'time_spent_seconds', 'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
        'attempt_number' => 'integer',
        'time_spent_seconds' => 'integer',
        'status' => 'string',
    ];

    // Relationships
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function responses()
    {
        return $this->hasMany(QuizResponse::class, 'attempt_id');
    }

    // Accessors
    public function getTimeSpentFormattedAttribute()
    {
        $minutes = floor($this->time_spent_seconds / 60);
        $seconds = $this->time_spent_seconds % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    // Helper Methods
    public function grade()
    {
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($this->responses as $response) {
            $question = $response->question;
            $totalPoints += $question->points;

            if ($response->is_correct) {
                $earnedPoints += $question->points;
            }
        }

        $percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;

        $this->update([
            'score' => $earnedPoints,
            'max_score' => $totalPoints,
            'percentage' => $percentage,
            'passed' => $percentage >= $this->quiz->passing_score,
            'status' => 'graded',
            'submitted_at' => now(),
        ]);

        return $this;
    }

    // Scopes
    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }
}