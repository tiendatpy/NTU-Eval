<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    public function titles()
    {
        return $this->hasMany(Title::class);
    }

    public function rewardResults()
    {
        return $this->hasMany(RewardResult::class);
    }
}