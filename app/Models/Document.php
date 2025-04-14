<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['type_id', 'file_name', 'file_path', 'uploaded_by', 'evaluation_detail_id'];

    public function type()
    {
        return $this->belongsTo(MetaType::class, 'type_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    public function evaluationDetail()
    {
        return $this->belongsTo(EvaluationDetail::class, 'evaluation_detail_id');
    }
} 