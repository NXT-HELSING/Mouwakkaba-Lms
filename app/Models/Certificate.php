<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'user_id', 'course_id', 'enrollment_id',
        'certificate_number', 'issued_at', 'expires_at',
        'pdf_path', 'template_used', 'verification_code', 'metadata',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificate) {
            if (empty($certificate->uuid)) {
                $certificate->uuid = (string) Str::uuid();
            }

            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = 'CERT-' . strtoupper(Str::random(10));
            }

            if (empty($certificate->verification_code)) {
                $certificate->verification_code = strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    // Accessors
    public function getPdfUrlAttribute()
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
    }

    public function getVerificationUrlAttribute()
    {
        return route('public.certificates.verify', $this->uuid);
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
}