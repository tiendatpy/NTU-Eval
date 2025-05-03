<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'evaluator_id', 'unit_id', 'period_id',
        'score', 'classification_id', 'status_id'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function classification()
    {
        return $this->belongsTo(MetaType::class, 'classification_id');
    }

    public function status()
    {
        return $this->belongsTo(MetaType::class, 'status_id');
    }

    public function details()
    {
        return $this->hasMany(EvaluationDetail::class);
    }

    public function period()
    {
        return $this->belongsTo(Periods::class, 'period_id');
    }

} 