<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCheckin extends Model
{
    use HasFactory;

    protected $table = 'user_checkins';

    protected $fillable = [
        'user_id',
        'checkin_date',
        'checkin_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
