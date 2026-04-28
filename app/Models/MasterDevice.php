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
    ];
}
