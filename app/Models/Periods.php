<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periods extends Model
{
    protected $fillable = [
        'year',
        'name',
        'start_date',
        'end_date',
        'status_id',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function status()
    {
        return $this->belongsTo(MetaType::class, 'status_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'period_id');
    }

    public function unitEvaluations()
    {
        return $this->hasMany(UnitEvaluation::class, 'period_id');
    }
}