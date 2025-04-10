<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'user_id', 'unit_id', 'evaluator_id', 'period',
        'score', 'classification_id', 'status_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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

} 