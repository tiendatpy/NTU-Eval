<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationDetail extends Model
{
    protected $fillable = ['evaluation_id', 'criteria_id', 'score', 'comments'];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function criteria()
    {
        return $this->belongsTo(EvaluationCriteria::class, 'criteria_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'evaluation_detail_id');
    }
} 