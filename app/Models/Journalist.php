<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Journalist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'photo',
        'job_title',
        'bio',
        'facebook',
        'instagram',
        'youtube',
        'x_twitter',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $appends = [
        'photo_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getArticleCountAttribute(): int
    {
        return $this->articles()->count();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (blank($this->photo)) {
            return null;
        }

        $photo = trim(str_replace('\\', '/', $this->photo));

        if (
            str_starts_with($photo, 'http://') ||
            str_starts_with($photo, 'https://')
        ) {
            return $photo;
        }

        $path = preg_replace(
            '#^(public/|storage/app/public/|storage/)#',
            '',
            ltrim($photo, '/')
        );

        return Storage::disk('public')->url($path);
    }

    public function getTotalViewsAttribute(): int
    {
        return (int) $this->articles()->sum('views');
    }
}