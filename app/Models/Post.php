<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'organization_id', 'title', 'content', 'type',
        'attachments', 'visibility', 'likes_count', 'comments_count',
        'shares_count', 'views_count', 'is_pinned', 'is_published',
        'published_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'views_count' => 'integer',
        'is_pinned' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'type' => 'string',
        'visibility' => 'string',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function likes()
    {
        return $this->morphMany(PostLike::class, 'likeable');
    }

    // Accessors
    public function getIsLikedByAttribute()
    {
        if (!auth()->check()) return false;

        return $this->likes()->where('user_id', auth()->id())->exists();
    }

    public function getExcerptAttribute()
    {
        return \Str::limit(strip_tags($this->content), 150);
    }

    public function getTimeAgoAttribute()
    {
        return $this->published_at?->diffForHumans() ?? $this->created_at->diffForHumans();
    }

    // Helper Methods
    public function toggleLike($userId)
    {
        $like = $this->likes()->where('user_id', $userId)->first();

        if ($like) {
            $like->delete();
            $this->decrement('likes_count');
            return false;
        }

        $this->likes()->create(['user_id' => $userId]);
        $this->increment('likes_count');
        return true;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('is_pinned', 'desc')
            ->orderBy('published_at', 'desc');
    }
}