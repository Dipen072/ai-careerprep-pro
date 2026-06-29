<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewQuestion extends Model
{
    protected $fillable = [
        'interview_session_id',
        'question_text',
        'user_answer',
        'ai_score',
        'ai_feedback_positive',
        'ai_feedback_missing',
        'ai_feedback_suggestions',
        'ai_improved_answer',
        'grammar_feedback',
        'voice_analysis',
        'camera_analysis',
    ];

    public function session()
    {
        return $this->belongsTo(InterviewSession::class, 'interview_session_id');
    }
}
