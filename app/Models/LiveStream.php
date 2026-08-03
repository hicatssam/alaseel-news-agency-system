<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LiveStream extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'embed_url',
        'description',
        'viewers_label',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Return the currently active stream or null.
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->latest()->first();
    }
}
