<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodingProblem extends Model
{
    protected $fillable = [
        'title',
        'description',
        'difficulty',
        'company_tags',
        'starter_code_php',
        'starter_code_js',
        'starter_code_python',
        'test_cases',
    ];

    protected $casts = [
        'test_cases' => 'array',
    ];

    public function submissions()
    {
        return $this->hasMany(CodingSubmission::class);
    }
}
