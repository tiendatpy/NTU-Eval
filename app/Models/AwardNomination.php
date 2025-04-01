<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwardNomination extends Model
{
    protected $fillable = ['user_id', 'unit_id', 'award_id', 'period', 'status_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function award()
    {
        return $this->belongsTo(Award::class);
    }

    public function status()
    {
        return $this->belongsTo(MetaType::class, 'status_id');
    }
} 