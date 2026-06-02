<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class SimulatorScenario extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'device_id',
        'device_name',
        'wattage',
        'current_hours',
        'scenario_hours',
        'tariff',
    ];
 
    protected $casts = [
        'current_hours'  => 'decimal:2',
        'scenario_hours' => 'decimal:2',
        'tariff'         => 'decimal:2',
    ];
 
    // ── Computed attributes ──────────────────────────
 
    // kWh aktual
    public function getCurrentKwhAttribute(): float
    {
        return round(($this->wattage * $this->current_hours) / 1000, 3);
    }
 
    // kWh skenario
    public function getScenarioKwhAttribute(): float
    {
        return round(($this->wattage * $this->scenario_hours) / 1000, 3);
    }
 
    // Cost aktual (Rp)
    public function getCurrentCostAttribute(): float
    {
        return round($this->current_kwh * $this->tariff, 0);
    }
 
    // Cost skenario (Rp)
    public function getScenarioCostAttribute(): float
    {
        return round($this->scenario_kwh * $this->tariff, 0);
    }
 
    // Selisih kWh
    public function getKwhSavingAttribute(): float
    {
        return round($this->current_kwh - $this->scenario_kwh, 3);
    }
 
    // Selisih cost (Rp)
    public function getCostSavingAttribute(): float
    {
        return round($this->current_cost - $this->scenario_cost, 0);
    }
 
    // Persentase penghematan
    public function getSavingPercentAttribute(): float
    {
        if ($this->current_cost == 0) return 0;
        return round(($this->cost_saving / $this->current_cost) * 100, 1);
    }
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}