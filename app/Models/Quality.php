<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quality extends Model
{
    protected $table = 'quality';

    protected $fillable = [
        'name',
        'description'
    ];

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function approvedEvaluations()
    {
        return $this->hasMany(Evaluation::class, 'approved_quality_id');
    }

    public function unitEvaluations()
    {
        return $this->hasMany(UnitEvaluation::class);
    }

    public function approvedUnitEvaluations()
    {
        return $this->hasMany(UnitEvaluation::class, 'approved_quality_id');
    }
}