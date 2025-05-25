<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type_id'
    ];

    public function type()
    {
        return $this->belongsTo(MetaType::class, 'type_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function approvedEvaluations()
    {
        return $this->hasMany(Evaluation::class, 'approved_title_id');
    }

    public function unitEvaluations()
    {
        return $this->hasMany(UnitEvaluation::class);
    }

    public function approvedUnitEvaluations()
    {
        return $this->hasMany(UnitEvaluation::class, 'approved_title_id');
    }
}