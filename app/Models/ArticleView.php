<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleView extends Model
{
    protected $fillable = [
        'article_id',
        'ip_hash',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}