<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'topic',
        'difficulty',
        'score',
        'total_questions',
        'correct_answers',
        'time_spent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
