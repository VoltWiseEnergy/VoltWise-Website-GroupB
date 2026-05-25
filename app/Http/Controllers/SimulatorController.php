<?php
 
namespace App\Services;
 
class SimulatorService
{
    // Hitung kWh dari wattage dan jam pemakaian
    public function calculateKwh(int $wattage, float $hours): float
    {
        return round(($wattage * $hours) / 1000, 3);
    }
 
    // Hitung cost dari kWh dan tariff
    public function calculateCost(float $kwh, float $tariff): float
    {
        return round($kwh * $tariff, 0);
    }
 
    // Hitung penghematan antara current dan skenario
    public function calculateSaving(float $currentCost, float $scenarioCost): array
    {
        $saving  = $currentCost - $scenarioCost;
        $percent = $currentCost > 0
            ? round(($saving / $currentCost) * 100, 1)
            : 0;
 
        return [
            'amount'  => round($saving, 0),
            'percent' => $percent,
            'isPositive' => $saving >= 0, // true = hemat, false = boros
        ];
    }
 
    // Hitung semua sekaligus untuk satu device
    public function compute(int $wattage, float $currentHours, float $scenarioHours, float $tariff): array
    {
        $currentKwh  = $this->calculateKwh($wattage, $currentHours);
        $scenarioKwh = $this->calculateKwh($wattage, $scenarioHours);
        $currentCost  = $this->calculateCost($currentKwh, $tariff);
        $scenarioCost = $this->calculateCost($scenarioKwh, $tariff);
        $saving       = $this->calculateSaving($currentCost, $scenarioCost);
 
        return [
            'current' => [
                'hours' => $currentHours,
                'kwh'   => $currentKwh,
                'cost'  => $currentCost,
            ],
            'scenario' => [
                'hours' => $scenarioHours,
                'kwh'   => $scenarioKwh,
                'cost'  => $scenarioCost,
            ],
            'saving' => $saving,
        ];
    }
}