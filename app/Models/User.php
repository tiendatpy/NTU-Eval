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
        'education_id',
        'role_id',
        'unit_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function education()
    {
        return $this->belongsTo(MetaType::class, 'education_id');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function approvedEvaluations()
    {
        return $this->hasMany(Evaluation::class, 'approved_by');
    }


    public function periods()
    {
        return $this->hasMany(Periods::class, 'created_by');
    }

    // Kiểm tra quyền người dùng
    public function isAdmin()
    {
        return $this->role->isSuperAdmin;
    }

    public function isUnitLeader()
    {
        return $this->role->isUnitLeader;
    }

    public function canManageEvaluations()
    {
        return $this->role->canManageEvaluations || $this->role->isSuperAdmin;
    }

    public function canApproveEvaluations()
    {
        return $this->role->canApproveEvaluations || $this->role->isSuperAdmin;
    }

    public function canExportReports()
    {
        return $this->role->canExportReports || $this->role->isSuperAdmin;
    }

    public function canManagePeriods()
    {
        return $this->role->canManagePeriods || $this->role->isSuperAdmin;
    }

    // Kiểm tra quyền trên đơn vị cụ thể
    public function hasUnitPermission($unitId)
    {
        return $this->role->isSuperAdmin || $this->unit_id == $unitId;
    }
}