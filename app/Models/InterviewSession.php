<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewSession extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'technology',
        'difficulty',
        'language',
        'score',
        'communication_score',
        'confidence_score',
        'status',
        'strong_areas',
        'weak_areas',
        'recommended_topics',
    ];

    protected function casts(): array
    {
        return [
            'strong_areas' => 'array',
            'weak_areas' => 'array',
            'recommended_topics' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(InterviewQuestion::class);
    }
}
