<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationCheck extends Model
{
    protected $fillable = [
        'user_id',
        'recommendation_key',
        'is_checked',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}