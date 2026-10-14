<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDailyMission extends Model
{
    use HasFactory;

    protected $table = 'user_daily_missions';

    protected $fillable = [
        'user_id',
        'mission_date',
        'mission_key',
        'is_completed',
        'reward_claimed',
    ];

    protected $casts = [
        'mission_date' => 'string',
        'is_completed' => 'boolean',
        'reward_claimed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
