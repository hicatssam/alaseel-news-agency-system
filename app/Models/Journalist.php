<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journalist extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','name','email','phone','photo','job_title','bio',
        'facebook','instagram','youtube','x_twitter','status'
    ];

    protected $casts = ['status' => 'boolean'];

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

    public function getTotalViewsAttribute(): int
    {
        return $this->articles()->sum('views');
    }
}
