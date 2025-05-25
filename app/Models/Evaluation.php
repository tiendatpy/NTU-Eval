<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'evaluator_id',
        'unit_id',
        'period_id',
        'rating',
        'quality_id',
        'approved_quality_id',
        'title_id',
        'approved_title_id',
        'reward_id',
        'approved_reward_id',
        'achievement',
        'comment',
        'review',
        'feedback',
        'status_id',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'approved_at' => 'datetime',
        'achievement' => 'string',
        'comment' => 'string',
        'review' => 'string',
        'feedback' => 'string',
    ];

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
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

    public function status()
    {
        return $this->belongsTo(MetaType::class, 'status_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details()
    {
        return $this->hasMany(EvaluationDetail::class);
    }
}