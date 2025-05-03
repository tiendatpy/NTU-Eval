<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaType extends Model
{
    protected $fillable = ['category', 'name'];

    public function units()
    {
        return $this->hasMany(Unit::class, 'type_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'classification_id');
    }

} 