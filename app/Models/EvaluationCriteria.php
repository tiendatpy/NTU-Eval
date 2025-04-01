<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationCriteria extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'weight'];

    public function category()
    {
        return $this->belongsTo(MetaType::class, 'category_id');
    }

    public function evaluationDetails()
    {
        return $this->hasMany(EvaluationDetail::class, 'criteria_id');
    }
} 