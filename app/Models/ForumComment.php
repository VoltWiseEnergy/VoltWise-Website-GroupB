<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ForumPost;

class ForumComment extends Model
{
    protected $fillable = [
        'forum_post_id',
        'user_id',
        'content',
        'votes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }
}