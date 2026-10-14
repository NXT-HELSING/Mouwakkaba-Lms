<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Lesson extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'chapter_id', 'title', 'slug', 'type', 'content',
        'video_url', 'video_duration', 'file_path', 'external_url',
        'order', 'duration_minutes', 'is_free_preview',
        'is_published', 'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_free_preview' => 'boolean',
        'is_published' => 'boolean',
        'order' => 'integer',
        'video_duration' => 'integer',
        'duration_minutes' => 'integer',
        'type' => 'string',
    ];

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'title']];
    }

    // Relationships
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function module()
    {
        return $this->hasOneThrough(Module::class, Chapter::class, 'id', 'id', 'chapter_id', 'module_id');
    }

    public function course()
    {
        return $this->hasOneThrough(
            Course::class,
            Module::class,
            'id',
            'id',
            'chapter_id',
            'course_id'
        )->through('chapter');
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    // Accessors
    public function getVideoEmbedUrlAttribute()
    {
        if (!$this->video_url) return null;

        // YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $this->video_url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return $this->video_url;
    }

    public function getIsVideoAttribute()
    {
        return $this->type === 'video';
    }

    public function getIsQuizAttribute()
    {
        return $this->type === 'quiz';
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

    public function scopeFreePreview($query)
    {
        return $query->where('is_free_preview', true);
    }
}