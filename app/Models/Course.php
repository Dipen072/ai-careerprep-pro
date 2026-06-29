<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'category',
        'description',
        'level',
        'lessons',
    ];

    protected $casts = [
        'lessons' => 'array',
    ];

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }
}
