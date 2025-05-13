<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'password', 
        'full_name', 
        'email',
        'date_of_birth',
        'phone', 
        'role_id', 
        'unit_id',
        'email_verified_at'
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'email_verified_at' => 'datetime'
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

    public function education()
    {
        return $this->belongsTo(MetaType::class, 'education_id');
    }

    public function evaluationsAsEvaluator()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }
}