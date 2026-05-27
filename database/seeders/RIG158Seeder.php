<?php

namespace Database\Seeders;

use App\Models\Rig;
use App\Models\Well;
use App\Models\Pump;
use App\Models\PumpAssembly;
use App\Models\AssemblyComponent;
use App\Models\PumpPersonnel;
use App\Models\DailyLog;
use App\Models\ComponentHour;
use App\Models\MaintenanceEvent;
use App\Services\ThresholdService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RIG158Seeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function() {
            // ── RIG ──────────────────────────────────────────────────────────
            $rig = Rig::create([
                'name'     => 'RIG158',
                'location' => 'Campo Cupiagua',
                'manager'  => 'Ricardo Sanchez / Wiston Ruiz',
            ]);

            // ── POZO ─────────────────────────────────────────────────────────
            $well = Well::create(['rig_id' => $rig->id, 'name' => 'CSB1643']);

            // ── BOMBA ────────────────────────────────────────────────────────
            $pump = Pump::create([
                'rig_id'                 => $rig->id,
                'number'                 => 3,
                'brand'                  => 'NATIONAL',
                'model'                  => '10-P-130',
                'serial'                 => '1020',
                'liner_diameter'         => '5-1/2 IN',
                'active_fixed_id'        => '08-01-009',
                'base_accumulated_hours' => 2060.25, // horas antes del inicio del registro
            ]);

            // ── PERSONAL ─────────────────────────────────────────────────────
            PumpPersonnel::create([
                'pump_id'          => $pump->id,
                'period_start'     => '2024-01-01',
                'rig_manager'      => 'Ricardo Sanchez / Wiston Ruiz',
                'supervisor_day'   => 'Edinson Chamorro',
                'supervisor_night' => 'Carlos Pérez',
                'encuellador_day'  => 'Juan Carlos Cardenez',
                'encuellador_night'=> 'Pedro García',
            ]);

            // ── CONJUNTOS Y COMPONENTES ───────────────────────────────────────
            // Horas iniciales (fila ANTERIOR del Excel — antes del día 1)
            $initialHours = [
                'left' => [
                    'camisa'           => 614.25,
                    'piston'           => 614.25,
                    'suction_module'   => ['serial' => '08-13-064', 'hours' => 1338.35],
                    'suction_valve'    => 1338.35,
                    'suction_seat'     => 1338.35,
                    'discharge_module' => ['serial' => '492555-2', 'hours' => 1745.05],
                    'discharge_valve'  => 1326.05,
                    'discharge_seat'   => 701.50,
                ],
                'middle' => [
                    'camisa'           => 614.25,
                    'piston'           => 614.25,
                    'suction_module'   => ['serial' => '08-13-066', 'hours' => 1338.35],
                    'suction_valve'    => 1338.35,
                    'suction_seat'     => 1338.35,
                    'discharge_module' => ['serial' => '492555-3', 'hours' => 1667.30],
                    'discharge_valve'  => 787.00,
                    'discharge_seat'   => 787.00, // Se cambia en día 1 → reset a 4.40
                ],
                'right' => [
                    'camisa'           => 614.25,
                    'piston'           => 614.29,
                    'suction_module'   => ['serial' => '08-13-070', 'hours' => 1338.35],
                    'suction_valve'    => 1338.35,
                    'suction_seat'     => 1338.35,
                    'discharge_module' => ['serial' => '492555-4', 'hours' => 1667.30],
                    'discharge_valve'  => 1326.05,
                    'discharge_seat'   => 1667.30,
                ],
            ];

            // Crear conjuntos y componentes
            $componentMap = []; // [position][type] => AssemblyComponent
            foreach (['left', 'middle', 'right'] as $position) {
                $assembly = PumpAssembly::create(['pump_id' => $pump->id, 'position' => $position]);

                foreach (ThresholdService::COMPONENT_ORDER as $type) {
                    $initData = $initialHours[$position][$type];
                    $serial     = null;
                    $initHours  = 0;

                    if (is_array($initData)) {
                        $serial    = $initData['serial'];
                        $initHours = $initData['hours'];
                    } else {
                        $initHours = $initData;
                    }

                    $thresh = ThresholdService::getThresholds($type);

                    $component = AssemblyComponent::create([
                        'assembly_id'              => $assembly->id,
                        'type'                     => $type,
                        'serial'                   => $serial,
                        'installed_at_hours'       => $initHours,
                        'alert_threshold_warning'  => $thresh['warning'],
                        'alert_threshold_critical' => $thresh['critical'],
                    ]);

                    $componentMap[$position][$type] = $component;
                }
            }

            // ── REGISTRO DIARIO — DÍAS 1 al 24 ───────────────────────────────
            $dailyData = [
                ['day'=>1,  'hours'=>4.40,  'accum'=>2064.65, 'dampener'=>900, 'comments'=>'CAMBIO ASIENTO DESCARGA #2'],
                ['day'=>2,  'hours'=>21.03, 'accum'=>2085.68, 'dampener'=>900, 'comments'=>''],
                ['day'=>3,  'hours'=>17.50, 'accum'=>2103.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>4,  'hours'=>16.00, 'accum'=>2119.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>5,  'hours'=>9.00,  'accum'=>2128.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>6,  'hours'=>5.00,  'accum'=>2133.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>7,  'hours'=>9.00,  'accum'=>2142.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>8,  'hours'=>6.00,  'accum'=>2148.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>9,  'hours'=>5.00,  'accum'=>2153.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>10, 'hours'=>0,     'accum'=>2153.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>11, 'hours'=>0,     'accum'=>2153.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>12, 'hours'=>15.00, 'accum'=>2168.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>13, 'hours'=>5.75,  'accum'=>2173.93, 'dampener'=>900, 'comments'=>''],
                ['day'=>14, 'hours'=>0,     'accum'=>2173.93, 'dampener'=>900, 'comments'=>''],
                ['day'=>15, 'hours'=>17.25, 'accum'=>2191.18, 'dampener'=>900, 'comments'=>''],
                ['day'=>16, 'hours'=>17.25, 'accum'=>2208.43, 'dampener'=>900, 'comments'=>''],
                ['day'=>17, 'hours'=>22.50, 'accum'=>2230.93, 'dampener'=>900, 'comments'=>''],
                ['day'=>18, 'hours'=>16.00, 'accum'=>2246.93, 'dampener'=>900, 'comments'=>''],
                ['day'=>19, 'hours'=>22.50, 'accum'=>2269.43, 'dampener'=>900, 'comments'=>''],
                ['day'=>20, 'hours'=>9.50,  'accum'=>2278.93, 'dampener'=>900, 'comments'=>''],
                ['day'=>21, 'hours'=>0,     'accum'=>2278.93, 'dampener'=>900, 'comments'=>''],
                ['day'=>22, 'hours'=>0,     'accum'=>2278.93, 'dampener'=>900, 'comments'=>''],
                ['day'=>23, 'hours'=>1.75,  'accum'=>2280.68, 'dampener'=>900, 'comments'=>''],
                ['day'=>24, 'hours'=>17.25, 'accum'=>2297.93, 'dampener'=>900, 'comments'=>''],
            ];

            // Horas acumuladas de componentes: arrancamos con los valores iniciales
            $runningHours = [];
            foreach ($componentMap as $position => $types) {
                foreach ($types as $type => $component) {
                    $runningHours[$component->id] = $component->installed_at_hours;
                }
            }

            foreach ($dailyData as $entry) {
                $logDate = '2024-01-' . str_pad($entry['day'], 2, '0', STR_PAD_LEFT);

                $log = DailyLog::create([
                    'pump_id'           => $pump->id,
                    'well_id'           => $well->id,
                    'log_date'          => $logDate,
                    'day_number'        => $entry['day'],
                    'hours_worked'      => $entry['hours'],
                    'accumulated_hours' => $entry['accum'],
                    'dampener_pressure' => $entry['dampener'],
                    'comments'          => $entry['comments'] ?: null,
                    'synced'            => true,
                ]);

                // En día 1: el asiento descarga del conjunto MEDIO se reemplaza
                $replacementDone = false;

                foreach ($componentMap as $position => $types) {
                    foreach ($types as $type => $component) {
                        // Caso especial: día 1, asiento descarga medio → reset
                        if ($entry['day'] === 1 && $position === 'middle' && $type === 'discharge_seat') {
                            $hoursBefore = $runningHours[$component->id];

                            // Maintenance event
                            MaintenanceEvent::create([
                                'daily_log_id' => $log->id,
                                'component_id' => $component->id,
                                'event_type'   => 'replacement',
                                'hours_before' => $hoursBefore,
                                'new_serial'   => null,
                                'notes'        => 'CAMBIO ASIENTO DESCARGA #2',
                            ]);

                            // Resetear horas al valor de hoursWorked del día
                            $runningHours[$component->id] = $entry['hours'];

                            // Actualizar installed_at_hours en el componente
                            $component->update(['installed_at_hours' => $entry['hours']]);
                        } else {
                            // Sumar horas trabajadas
                            $runningHours[$component->id] += $entry['hours'];
                        }

                        ComponentHour::create([
                            'daily_log_id'      => $log->id,
                            'component_id'      => $component->id,
                            'hours_accumulated' => round($runningHours[$component->id], 2),
                        ]);
                    }
                }
            }

            $this->command->info("✅ RIG158 / CSB1643 / Bomba NATIONAL 10-P-130 creada con 24 días de registro.");
        });
    }
}
