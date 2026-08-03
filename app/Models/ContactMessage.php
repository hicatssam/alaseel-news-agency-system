<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = ['name','email','phone','subject','message','status'];

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }
}
