<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $fillable = ['name', 'description', 'level_id'];

    public function level()
    {
        return $this->belongsTo(MetaType::class, 'level_id');
    }

    public function nominations()
    {
        return $this->hasMany(AwardNomination::class);
    }
} 