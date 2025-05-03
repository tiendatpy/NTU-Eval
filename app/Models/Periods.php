<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periods extends Model
{

    protected $fillable = ['year'];

    // Quan hệ với Evaluation
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'period_id');
    }

    // Quan hệ với TitleNomination
    public function titleNominations()
    {
        return $this->hasMany(TitleNomination::class, 'period_id');
    }
}