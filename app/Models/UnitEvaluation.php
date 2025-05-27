<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitEvaluation extends Model
{
    protected $fillable = [
        'unit_id',
        'period_id',
        'evaluator_id',
        'approved_by',
        'quality_id',
        'title_id',
        'reward_id',
        'evidence',
        'achievement',
        'approved_quality_id',
        'approved_title_id',
        'approved_reward_id',
        'approved_evidence',
        'approved_achievement'
    ];

    protected $casts = [
        'evidence' => 'string',
        'achievement' => 'string',
        'approved_evidence' => 'string',
        'approved_achievement' => 'string',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function period()
    {
        return $this->belongsTo(Periods::class, 'period_id');
    }

    public function quality()
    {
        return $this->belongsTo(Quality::class);
    }

    public function approvedQuality()
    {
        return $this->belongsTo(Quality::class, 'approved_quality_id');
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function approvedTitle()
    {
        return $this->belongsTo(Title::class, 'approved_title_id');
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function approvedReward()
    {
        return $this->belongsTo(Reward::class, 'approved_reward_id');
    }
}