<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'password', 'full_name', 'email', 
        'phone', 'role_id', 'unit_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function evaluationsAsEvaluator()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function awardNominations()
    {
        return $this->hasMany(AwardNomination::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }
} 