<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','file_name','file_path','file_type',
        'mime_type','size','alt_text','caption','folder'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('file_type', 'video');
    }

    public function getFormattedSizeAttribute(): string
    {
        $size = $this->size ?? 0;
        if ($size >= 1073741824) return number_format($size / 1073741824, 2) . ' GB';
        if ($size >= 1048576) return number_format($size / 1048576, 2) . ' MB';
        if ($size >= 1024) return number_format($size / 1024, 2) . ' KB';
        return $size . ' B';
    }
}
