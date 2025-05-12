<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quality extends Model
{
    protected $table = 'quality';

    protected $fillable = [
        'name',
        'description',
    ];

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'quality_id');
    }

    public function approvedEvaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'approved_quality_id');
    }
}
