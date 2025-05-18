<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'type_id'];

    public function type()
    {
        return $this->belongsTo(MetaType::class, 'type_id');
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }

} 