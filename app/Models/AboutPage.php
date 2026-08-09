<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AboutPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'vision',
        'mission',
        'values',
        'image',
        'is_active',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!filled($this->image)) {
            return null;
        }

        $image = trim(str_replace('\\', '/', $this->image));

        if (
            Str::startsWith($image, [
                'http://',
                'https://',
                '//',
            ])
        ) {
            return $image;
        }

        if (Str::startsWith($image, '/storage/')) {
            return url($image);
        }

        if (Str::startsWith($image, 'storage/')) {
            return asset($image);
        }

        $image = Str::after($image, 'storage/app/public/');
        $image = Str::after($image, 'public/');

        return Storage::disk('public')->url(
            ltrim($image, '/')
        );
    }
}