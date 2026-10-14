<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Course extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $fillable = [
        'title', 'slug', 'subtitle', 'description', 'objectives', 'prerequisites',
        'thumbnail', 'promo_video', 'level', 'duration_minutes', 'language',
        'price', 'is_free', 'is_published', 'published_at', 'max_students',
        'certificate_enabled', 'certificate_template', 'passing_score',
        'category_id', 'instructor_id', 'created_by',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'certificate_enabled' => 'boolean',
        'published_at' => 'datetime',
        'price' => 'decimal:2',
        'level' => 'string',
    ];

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'title']];
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class, 'id', 'chapter_id', 'id', 'id')
            ->join('modules', 'chapters.module_id', '=', 'modules.id')
            ->where('modules.course_id', $this->id);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('progress_percentage', 'status', 'enrolled_at')
            ->withTimestamps();
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    // Accessors
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail
            ? asset('storage/' . $this->thumbnail)
            : asset('images/course-placeholder.jpg');
    }

    public function getStudentsCountAttribute()
    {
        return $this->enrollments()->count();
    }

    public function getDurationHoursAttribute()
    {
        return round($this->duration_minutes / 60, 1);
    }

    public function getLevelLabelAttribute()
    {
        return match ($this->level) {
            'beginner' => 'Débutant',
            'intermediate' => 'Intermédiaire',
            'advanced' => 'Avancé',
            default => 'Non défini',
        };
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopeSearch($query, $term)
    {
        return $query->whereRaw("MATCH(title, subtitle, description) AGAINST(? IN NATURAL LANGUAGE MODE)", [$term]);
    }
}