<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitHead extends Model
{
    protected $fillable = ['unit_id', 'head_id'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_id');
    }
} 