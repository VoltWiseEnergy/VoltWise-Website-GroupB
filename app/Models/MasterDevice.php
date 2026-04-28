<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterDevice extends Model
{
    protected $fillable = [
        'name',
        'category',
        'wattage',
        'description',
        'hours_per_day',
    ];

    public function getDailyEnergyKwhAttribute(): float
    {
        $hours = $this->hours_per_day ?? $this->usage_hours ?? 1;
        return (float) round(($this->wattage / 1000) * $hours, 3);
    }
}
