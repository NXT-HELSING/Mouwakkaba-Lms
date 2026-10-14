<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'module_id', 'chapter_id', 'lesson_id',
        'title', 'description', 'type', 'passing_score',
        'max_attempts', 'time_limit_minutes', 'randomize_questions',
        'show_results_immediately', 'is_published', 'created_by',
    ];

    protected $casts = [
        'randomize_questions' => 'boolean',
        'show_results_immediately' => 'boolean',
        'is_published' => 'boolean',
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
        'time_limit_minutes' => 'integer',
        'type' => 'string',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getTotalPointsAttribute()
    {
        return $this->questions()->sum('points');
    }

    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'quiz' => 'Quiz',
            'pre_test' => 'Pré-test',
            'post_test' => 'Post-test',
            'exam' => 'Examen',
            default => 'Quiz',
        };
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}