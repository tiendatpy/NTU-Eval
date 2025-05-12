<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    protected $fillable = [
        'evaluator_id', 'unit_id', 'period_id',
        'rating', 'quality_id', 'approved_quality_id',
        'title_id', 'approved_title_id', 'reward_id', 'achievement',
        'comment', 'review', 'feedback', 'status_id'
    ];

    // ------------------ Relationships ------------------

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function quality(): BelongsTo
    {
        return $this->belongsTo(Quality::class, 'quality_id');
    }

    public function approvedQuality(): BelongsTo
    {
        return $this->belongsTo(Quality::class, 'approved_quality_id');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class, 'title_id');
    }

    public function approvedTitle(): BelongsTo
    {
        return $this->belongsTo(Title::class, 'approved_title_id');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(Reward::class, 'reward_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(MetaType::class, 'status_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Periods::class, 'period_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(EvaluationDetail::class);
    }
}
