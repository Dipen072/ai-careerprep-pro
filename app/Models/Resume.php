<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    protected $fillable = [
        'user_id',
        'file_path',
        'ats_score',
        'extracted_skills',
        'missing_skills',
        'suggestions',
        'projects',
        'full_analysis',
    ];

    protected $casts = [
        'full_analysis' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
