<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'isSuperAdmin',
        'canManageEvaluations',
        'canApproveEvaluations',
        'canExportReports',
        'isUnitLeader',
        'canManagePeriods'
    ];

    protected $casts = [
        'isSuperAdmin' => 'boolean',
        'canManageEvaluations' => 'boolean',
        'canApproveEvaluations' => 'boolean',
        'canExportReports' => 'boolean',
        'isUnitLeader' => 'boolean',
        'canManagePeriods' => 'boolean'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}