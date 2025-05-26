<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationCriteria extends Model
{
    protected $table = 'evaluation_criteria';
    protected $fillable = [
        'category_id',
        'name'
    ];

    public function category()
    {
        return $this->belongsTo(MetaType::class, 'category_id');
    }

    public function details()
    {
        return $this->hasMany(EvaluationDetail::class, 'criteria_id');
    }
}