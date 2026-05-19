<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPointLog extends Model
{
    protected $fillable = [
        'user_id',
        'event_type',
        'points',
        'log_date',
    ];

    protected $casts = [
        'log_date' => 'date',
        'points'   => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
