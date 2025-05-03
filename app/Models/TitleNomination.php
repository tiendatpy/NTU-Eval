<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitleNomination extends Model
{
    protected $fillable = [
        'user_id',
        'unit_id',
        'title_id',
        'approved_title_id',
        'period_id',
        'status_id',
        'reward_id',
        'achievement',
        'review'
    ];

    protected $casts = [
        'period' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }
    
    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function status()
    {
        return $this->belongsTo(MetaType::class, 'status_id');
    }

    public function approvedTitle()
    {
        return $this->belongsTo(Title::class, 'approved_title_id');
    }

    public function period()
    {
        return $this->belongsTo(Periods::class, 'period_id');
    }
}