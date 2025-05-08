<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'type_id',
    ];

    public function type()
    {
        return $this->belongsTo(MetaType::class, 'type_id');
    }


    public function nominations()
    {
        return $this->hasMany(TitleNomination::class);
    }

    public function rewardResults()
    {
        return $this->hasMany(RewardResult::class);
    }
}