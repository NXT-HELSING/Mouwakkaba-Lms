<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'course_id', 'organization_id', 'enrolled_at',
        'started_at', 'completed_at', 'progress_percentage',
        'last_lesson_id', 'last_activity_at', 'status',
        'completion_certificate_id',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'status' => 'string',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function lastLesson()
    {
        return $this->belongsTo(Lesson::class, 'last_lesson_id');
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function certificate()
    {
        return $this->belongsTo(Certificate::class, 'completion_certificate_id');
    }

    // Accessors
    public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    public function getProgressRoundedAttribute()
    {
        return round($this->progress_percentage);
    }

    // Helper Methods
    public function recalculateProgress()
    {
        $totalLessons = $this->course->modules()
            ->with('chapters.lessons')
            ->get()
            ->pluck('chapters.*.lessons.*')
            ->flatten()
            ->count();

        if ($totalLessons === 0) {
            $this->update(['progress_percentage' => 0]);
            return;
        }

        $completedLessons = $this->lessonProgress()
            ->where('status', 'completed')
            ->count();

        $percentage = ($completedLessons / $totalLessons) * 100;

        $this->update([
            'progress_percentage' => $percentage,
            'last_activity_at' => now(),
        ]);

        if ($percentage >= 100 && $this->status !== 'completed') {
            $this->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}