<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'title', 'description', 'order',
        'duration_minutes', 'is_published', 'unlock_after_module_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
        'duration_minutes' => 'integer',
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function unlockAfter()
    {
        return $this->belongsTo(Module::class, 'unlock_after_module_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    // Accessors
    public function getLessonsCountAttribute()
    {
        return $this->chapters->sum(fn($c) => $c->lessons->count());
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}