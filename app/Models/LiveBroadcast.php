<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveBroadcast extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','title','description','platform','stream_url',
        'embed_code','status','started_at','ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }
}
