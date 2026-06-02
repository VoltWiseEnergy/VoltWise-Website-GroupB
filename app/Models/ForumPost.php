<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ForumComment;
use App\Models\ForumReport;

class ForumPost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'votes',
        'status',
        'is_verified',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'forum_post_id');
    }

    public function reports()
    {
        return $this->hasMany(ForumReport::class, 'post_id');
    }
}
