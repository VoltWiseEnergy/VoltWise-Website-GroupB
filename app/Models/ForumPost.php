<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ForumComment;
use App\Models\ForumPostVote;

class ForumPost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'votes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'forum_post_id');
    }

    public function uservotes()
    {
        return $this->hasMany(ForumPostVote::class, 'forum_post_id');
    }
}