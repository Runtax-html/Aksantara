<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserUnlockedStory extends Model
{
    use HasFactory;

    protected $table = 'user_unlocked_stories';

    protected $fillable = [
        'user_id',
        'story_id',
        'is_read',
        'unlocked_at',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'unlocked_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
