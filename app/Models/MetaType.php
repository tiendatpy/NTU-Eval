<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaType extends Model
{
    protected $fillable = [
        'category',
        'name'
    ];

    // Các relationship
    public function units()
    {
        return $this->hasMany(Unit::class, 'type_id');
    }

    public function educatedUsers()
    {
        return $this->hasMany(User::class, 'education_id');
    }

    public function evaluationCriteria()
    {
        return $this->hasMany(EvaluationCriteria::class, 'category_id');
    }

    public function titles()
    {
        return $this->hasMany(Title::class, 'type_id');
    }


    public function evaluationStatuses()
    {
        return $this->hasMany(Evaluation::class, 'status_id');
    }

    public function periodStatuses()
    {
        return $this->hasMany(Periods::class, 'status_id');
    }

    // Helpers để lấy meta types theo category
    public static function getByCategory($category)
    {
        return self::where('category', $category)->get();
    }

    public static function getByCategoryAndName($category, $name)
    {
        return self::where('category', $category)
            ->where('name', $name)
            ->first();
    }
}