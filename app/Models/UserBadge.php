<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    protected $fillable = [
        'user_id',
        'badge_name',
        'badge_icon',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
