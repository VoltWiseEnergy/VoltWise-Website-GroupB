<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'wattage',
        'hours',
        'usage_date',
        'is_override',
    ];

    protected $casts = [
        'usage_date'  => 'date',
        'is_override' => 'boolean',
        'hours'       => 'decimal:2',
    ];

    // kWh dihitung otomatis, tidak disimpan di DB
    public function getKwhAttribute(): float
    {
        return round(($this->wattage * $this->hours) / 1000, 3);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}