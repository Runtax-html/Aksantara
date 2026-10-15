<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuizProgress extends Model
{
    use HasFactory;

    protected $table = 'user_quiz_progress';

    protected $fillable = [
        'user_id',
        'level_id',
        'status',
        'stars',
        'score',
        'total_questions',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'stars' => 'integer',
        'score' => 'integer',
        'total_questions' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
