<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'parent_id','name','slug','description','image','status','sort_order',
        'show_in_header','show_in_footer','show_on_homepage','color',
    ];

    protected $casts = [
        'status'          => 'boolean',
        'show_in_header'  => 'boolean',
        'show_in_footer'  => 'boolean',
        'show_on_homepage'=> 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeForHeader($query)
    {
        return $query->where('status', true)->where('show_in_header', true)->orderBy('sort_order');
    }

    public function scopeForFooter($query)
    {
        return $query->where('status', true)->where('show_in_footer', true)->orderBy('sort_order');
    }

    public function scopeForHomepage($query)
    {
        return $query->where('status', true)->where('show_on_homepage', true)->orderBy('sort_order');
    }
}
