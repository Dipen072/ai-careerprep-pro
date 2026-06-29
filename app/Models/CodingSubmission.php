<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodingSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'coding_problem_id',
        'language',
        'submitted_code',
        'status',
        'test_cases_passed',
        'total_test_cases',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function problem()
    {
        return $this->belongsTo(CodingProblem::class, 'coding_problem_id');
    }
}
