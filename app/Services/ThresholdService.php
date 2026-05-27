<?php

namespace App\Services;

class ThresholdService
{
    // Umbrales típicos para bombas triplex de lodo (horas de servicio)
    public const THRESHOLDS = [
        'camisa'           => ['warning' => 800,  'critical' => 1000],
        'piston'           => ['warning' => 800,  'critical' => 1000],
        'suction_module'   => ['warning' => 1400, 'critical' => 1800],
        'suction_valve'    => ['warning' => 1400, 'critical' => 1800],
        'suction_seat'     => ['warning' => 1400, 'critical' => 1800],
        'discharge_module' => ['warning' => 1800, 'critical' => 2000],
        'discharge_valve'  => ['warning' => 1400, 'critical' => 1800],
        'discharge_seat'   => ['warning' => 700,  'critical' => 900 ],
    ];

    public static function getStatus(string $type, float $hours): string
    {
        $t = self::THRESHOLDS[$type] ?? null;
        if (!$t) return 'ok';
        if ($hours >= $t['critical']) return 'critical';
        if ($hours >= $t['warning'])  return 'warning';
        return 'ok';
    }

    public static function getThresholds(string $type): array
    {
        return self::THRESHOLDS[$type] ?? ['warning' => null, 'critical' => null];
    }

    public static function getStatusBadgeClass(string $status): string
    {
        return match($status) {
            'critical' => 'bg-red-100 text-red-800 border-red-300',
            'warning'  => 'bg-amber-100 text-amber-800 border-amber-300',
            default    => 'bg-green-100 text-green-800 border-green-300',
        };
    }

    public static function getStatusLabel(string $status): string
    {
        return match($status) {
            'critical' => '🔴 Crítico',
            'warning'  => '⚠ Alerta',
            default    => '✓ OK',
        };
    }

    /** Orden canónico de los 8 tipos de componente */
    public const COMPONENT_ORDER = [
        'camisa', 'piston',
        'suction_module', 'suction_valve', 'suction_seat',
        'discharge_module', 'discharge_valve', 'discharge_seat',
    ];
}
