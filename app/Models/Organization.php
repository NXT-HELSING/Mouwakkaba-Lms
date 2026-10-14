<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Organization extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $fillable = [
        'name', 'slug', 'description', 'logo', 'email', 'phone',
        'website', 'address', 'city', 'region', 'type', 'status', 'created_by',
    ];

    protected $casts = [
        'status' => 'string',
        'type' => 'string',
    ];

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Accessors
    public function getLogoUrlAttribute()
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1F3864&color=fff';
    }

    public function getMembersCountAttribute()
    {
        return $this->users()->count();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }
}