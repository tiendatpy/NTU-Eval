<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitleNomination extends Model
{
    protected $fillable = [
        'user_id',
        'unit_id',
        'title_id',
        'period',
        'status_id',
        'reward_id',
        'achievement',
    ];

    protected $casts = [
        'period' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function status()
    {
        return $this->belongsTo(MetaType::class, 'status_id');
    }
}