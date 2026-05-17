<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
    'user_id',
    'name',
    'wattage',
    'category',
    'daily_energy_kwh',
    'tariff' // calculated, not inputable
    ];

}