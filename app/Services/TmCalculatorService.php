<?php

namespace App\Services;

class TmCalculatorService
{
    const FACTORES = [
        '1'  => 1.0,
        '1B' => 0.5,
        '2'  => 1.0,
        '3'  => 1.0,
        '4'  => 1.0,
        '5'  => 1.0,
        '6'  => 1.0,
        '7'  => 1.0,
        '8'  => 1.0,
        '9'  => 1.0,
        '10' => 1.0,
        '11' => 1.0,
        '12' => 1.0,
        '13' => 0.0,
        '14' => 0.0,
    ];

    const OPERACIONES = [
        '1'  => 'COD 1  — POOH / RIH (Bajando/Sacando sarta)',
        '1B' => 'COD 1B — Short Trip',
        '2'  => 'COD 2  — Drilling con Top Drive',
        '3'  => 'COD 3  — Drilling + Reaming con Top Drive',
        '4'  => 'COD 4  — Coring con Top Drive',
        '5'  => 'COD 5  — Running Casing',
        '6'  => 'COD 6  — Working Casing',
        '7'  => 'COD 7  — Jarring Down',
        '8'  => 'COD 8  — Jarring Up',
        '9'  => 'COD 9  — Pulling on Stuck Pipe',
        '10' => 'COD 10 — Drilling con Kelly',
        '11' => 'COD 11 — Drilling + Reaming con Kelly',
        '12' => 'COD 12 — Coring con Kelly',
        '13' => 'COD 13 — Giro de cable (hidráulico)',
        '14' => 'COD 14 — Corte / Reemplazo de cable',
    ];

    const OPERACIONES_EN = [
        '1'  => 'COD 1  — POOH / RIH (Pulling Out / Running In Hole)',
        '1B' => 'COD 1B — Short Trip',
        '2'  => 'COD 2  — Drilling with Top Drive',
        '3'  => 'COD 3  — Drilling + Reaming with Top Drive',
        '4'  => 'COD 4  — Coring with Top Drive',
        '5'  => 'COD 5  — Running Casing',
        '6'  => 'COD 6  — Working Casing',
        '7'  => 'COD 7  — Jarring Down',
        '8'  => 'COD 8  — Jarring Up',
        '9'  => 'COD 9  — Pulling on Stuck Pipe',
        '10' => 'COD 10 — Drilling with Kelly',
        '11' => 'COD 11 — Drilling + Reaming with Kelly',
        '12' => 'COD 12 — Coring with Kelly',
        '13' => 'COD 13 — Cable Rotation (Hydraulic)',
        '14' => 'COD 14 — Cut / Replace Cable',
    ];

    /**
     * Calcula TM para una operación.
     *
     * TM = Fop × Fa × Pe × D_media / 1_000_000
     *
     * Fa  = 1 / lineas_activas
     * Fb  = 1 − (densidad_lodo / 65.4)   [factor de flotabilidad — acero]
     * Pe  = Fb × (peso_dp × long_dp + peso_bha × long_bha) + peso_bloque
     * D_media = (prof_ini + prof_fin) / 2
     */
    public static function calcular(array $input): array
    {
        $cod = (string) ($input['cod'] ?? '1');

        if (in_array($cod, ['13', '14'])) {
            return self::emptyResult($cod);
        }

        $fop    = self::FACTORES[$cod] ?? 1.0;
        $lineas = max(1, (int) ($input['lineas_activas'] ?? 8));
        $fa     = 1 / $lineas;

        $densidad = (float) ($input['densidad_lodo_ppg'] ?? 10);
        $fb       = 1 - ($densidad / 65.4);

        $profFin  = (float) ($input['prof_final_ft']  ?? 0);
        $longBha  = (float) ($input['long_bha_ft']    ?? 0);
        $longDp   = max(0, $profFin - $longBha);

        $pesoDp     = (float) ($input['peso_dp_lb_ft']  ?? 0);
        $pesoBha    = (float) ($input['peso_bha_lb_ft'] ?? 0);
        $pesoBloque = (float) ($input['peso_bloque_lb'] ?? 25000);

        $pe     = $fb * ($pesoDp * $longDp + $pesoBha * $longBha) + $pesoBloque;
        $dMedia = ((float) ($input['prof_inicial_ft'] ?? 0) + $profFin) / 2;

        $tm = $fop * $fa * $pe * $dMedia / 1_000_000;

        return [
            'tm'         => round(max(0, $tm), 4),
            'factor_op'  => $fop,
            'fa'         => round($fa, 6),
            'fb'         => round($fb, 4),
            'pe'         => round($pe, 2),
            'd_media'    => round($dMedia, 2),
            'es_corte'   => false,
            'es_giro'    => false,
        ];
    }

    private static function emptyResult(string $cod): array
    {
        return [
            'tm'        => 0,
            'factor_op' => 0,
            'fa'        => 0,
            'fb'        => 0,
            'pe'        => 0,
            'd_media'   => 0,
            'es_corte'  => $cod === '14',
            'es_giro'   => $cod === '13',
        ];
    }

    public static function gaugeArc(float $pct, float $r = 80): array
    {
        $pct   = min(100, max(0, $pct));
        $cx    = 100; $cy = 100;
        $angle = M_PI - ($pct / 100) * M_PI; // left=180° → right=0°
        $x     = round($cx + $r * cos($angle), 2);
        $y     = round($cy - $r * sin($angle), 2);
        $large = $pct > 50 ? 1 : 0;
        $path  = "M " . ($cx - $r) . " $cy A $r $r 0 $large 1 $x $y";

        $color = $pct >= 95 ? '#FF4D2E' : ($pct >= 80 ? '#EAB308' : '#06B6D4');

        return compact('path', 'color', 'pct', 'x', 'y');
    }
}
