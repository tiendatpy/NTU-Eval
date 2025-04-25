<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardResult extends Model
{
    protected $fillable = [
        'user_id',
        'title_id',
        'reward_id',
        'issued_date'
    ];

    protected $casts = [
        'issued_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
}