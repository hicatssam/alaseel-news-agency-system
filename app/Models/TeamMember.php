<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeamMember extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'job_title',
        'image',
        'display_order',
        'is_active',
    ];

    protected $appends = [
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('display_order')
            ->orderBy('id');
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