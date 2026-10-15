<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Resource extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $fillable = [
        'title', 'slug', 'description', 'type', 'file_path',
        'external_url', 'file_size', 'mime_type', 'thumbnail',
        'category_id', 'tags', 'language', 'is_public',
        'is_downloadable', 'download_count', 'view_count',
        'status', 'created_by',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_public' => 'boolean',
        'is_downloadable' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'type' => 'string',
        'status' => 'string',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail
            ? asset('storage/' . $this->thumbnail)
            : asset('images/resource-placeholder.png');
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) return 'N/A';

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function getTypeIconAttribute()
    {
        return match ($this->type) {
            'pdf' => 'file-pdf',
            'doc' => 'file-word',
            'video' => 'file-video',
            'audio' => 'file-audio',
            'image' => 'file-image',
            'link' => 'link',
            'zip' => 'file-archive',
            default => 'file',
        };
    }

    // Helper Methods
    public function incrementDownload()
    {
        $this->increment('download_count');
    }

    public function incrementView()
    {
        $this->increment('view_count');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->whereRaw("MATCH(title, description) AGAINST(? IN NATURAL LANGUAGE MODE)", [$term]);
    }
}