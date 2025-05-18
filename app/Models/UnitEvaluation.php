<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitEvaluation extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unit_id',
        'quality_id',
        'title_id',
        'reward_id',
        'period_id',
        'evidence',
        'achievement',
    ];

    /**
     * Get the unit that owns the evaluation.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the quality associated with the evaluation.
     */
    public function quality(): BelongsTo
    {
        return $this->belongsTo(Quality::class);
    }

    /**
     * Get the title associated with the evaluation.
     */
    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class);
    }

    /**
     * Get the reward associated with the evaluation.
     */
    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class);
    }

    /**
     * Get the period associated with the evaluation.
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(Periods::class);
    }
}