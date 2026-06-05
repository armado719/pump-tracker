<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 8px; margin: 10px; color: #111; }
  h1 { font-size: 11px; text-align: center; margin-bottom: 6px; letter-spacing: 1px; }
  .subtitle { font-size: 8px; text-align: center; color: #555; margin-bottom: 10px; }
  .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .info-table td { padding: 3px 6px; border: 1px solid #ccc; font-size: 7.5px; }
  .info-table .lbl { font-size: 6px; color: #666; text-transform: uppercase; display: block; margin-bottom: 1px; }
  .summary-box { border: 2px solid #0e7490; border-radius: 4px; padding: 6px 10px; margin-bottom: 10px;
                 display: table; width: 100%; }
  .summary-cell { display: table-cell; text-align: center; padding: 4px 8px; border-right: 1px solid #b2e0ea; }
  .summary-cell:last-child { border-right: none; }
  .summary-val { font-size: 14px; font-weight: bold; color: #0e7490; }
  .summary-lbl { font-size: 6px; color: #666; text-transform: uppercase; }
  .summary-val.warn { color: #d97706; }
  .summary-val.crit { color: #dc2626; }
  table.ops { width: 100%; border-collapse: collapse; }
  table.ops th { background: #0e7490; color: white; padding: 3px 2px; text-align: center;
                 font-size: 6.5px; border: 1px solid #0c5f75; }
  table.ops td { padding: 2px 3px; border: 1px solid #d1d5db; text-align: center; font-size: 7px; }
  table.ops tr:nth-child(even) { background: #f0f9ff; }
  .total-row { background: #cffafe !important; font-weight: bold; }
  .section-title { background: #164e63; color: white; padding: 3px 6px; font-size: 8px;
                   font-weight: bold; margin: 10px 0 4px 0; letter-spacing: 0.5px; }
  .bar-wrap { display: inline-block; width: 80px; height: 6px; background: #e5e7eb;
              border-radius: 3px; vertical-align: middle; }
  .bar-fill { height: 6px; border-radius: 3px; }
  .footer { font-size: 6.5px; color: #888; margin-top: 10px; text-align: right; }
  .cut-row { background: #fff1f2 !important; }
</style>
</head>
<body>

<h1>CONTROL DE TONELADAS MILLA (TM) — {{ strtoupper($cable->rig->name) }}</h1>
<div class="subtitle">Cable: {{ $cable->serial }} | Grado: {{ $cable->grado }} | Diám.: {{ $cable->diametro_in ?? '—' }}</div>

{{-- Info cable --}}
<table class="info-table">
<tr>
  <td><span class="lbl">RIG</span>{{ $cable->rig->name }}</td>
  <td><span class="lbl">Ubicación</span>{{ $cable->rig->location ?? '—' }}</td>
  <td><span class="lbl">Serial cable</span>{{ $cable->serial }}</td>
  <td><span class="lbl">Fabricante</span>{{ $cable->fabricante ?? '—' }}</td>
  <td><span class="lbl">Referencia</span>{{ $cable->referencia ?? '—' }}</td>
  <td><span class="lbl">Grado</span>{{ $cable->grado }}</td>
  <td><span class="lbl">Diámetro</span>{{ $cable->diametro_in ?? '—' }}</td>
  <td><span class="lbl">Resistencia</span>{{ $cable->resistencia_lb ? number_format($cable->resistencia_lb,0).' lb' : '—' }}</td>
</tr>
<tr>
  <td><span class="lbl">Instalación</span>{{ $cable->fecha_instalacion->format('d/m/Y') }}</td>
  <td><span class="lbl">Long. inicial</span>{{ $cable->longitud_inicial_ft ? number_format($cable->longitud_inicial_ft,0).' ft' : '—' }}</td>
  <td><span class="lbl">TM máx. corte</span>{{ number_format($cable->rig->tm_max_corte ?? 1200, 0) }} TM</td>
  <td><span class="lbl">Líneas activas</span>{{ $cable->rig->lineas_activas ?? 8 }}</td>
  <td><span class="lbl">Estado</span>{{ $cable->activo ? 'ACTIVO' : 'ARCHIVADO' }}</td>
  <td colspan="3"><span class="lbl">N° operaciones</span>{{ $cable->operaciones->count() }}</td>
</tr>
</table>

{{-- Resumen TM --}}
@php
  $pctColor = $tmPct >= 95 ? 'crit' : ($tmPct >= ($cable->rig->tm_alerta_pct ?? 80) ? 'warn' : '');
@endphp
<div class="summary-box">
  <div class="summary-cell">
    <div class="summary-val">{{ number_format($tmAcum, 2) }}</div>
    <div class="summary-lbl">TM Acumulado</div>
  </div>
  <div class="summary-cell">
    <div class="summary-val">{{ number_format($tmMax, 0) }}</div>
    <div class="summary-lbl">TM Máximo</div>
  </div>
  <div class="summary-cell">
    <div class="summary-val {{ $pctColor }}">{{ $tmPct }}%</div>
    <div class="summary-lbl">Vida útil usada</div>
  </div>
  <div class="summary-cell">
    <div class="summary-val">{{ number_format(max(0, $tmMax - $tmAcum), 2) }}</div>
    <div class="summary-lbl">TM Restantes</div>
  </div>
  <div class="summary-cell">
    <div class="summary-val">{{ $cable->operaciones->count() }}</div>
    <div class="summary-lbl">Operaciones</div>
  </div>
  <div class="summary-cell">
    <div class="summary-val">{{ $cable->cortes->count() }}</div>
    <div class="summary-lbl">Cortes</div>
  </div>
</div>

{{-- Tabla de operaciones --}}
<div class="section-title">▶ HISTORIAL DE OPERACIONES</div>

<table class="ops">
<thead>
<tr>
  <th>#</th>
  <th>Fecha</th>
  <th>COD</th>
  <th style="text-align:left;min-width:120px">Tipo de Operación</th>
  <th>Prof. Ini (ft)</th>
  <th>Prof. Fin (ft)</th>
  <th>Dens. Lodo (ppg)</th>
  <th>Peso DP (lb/ft)</th>
  <th>Peso BHA (lb/ft)</th>
  <th>Long. BHA (ft)</th>
  <th>Peso Bloque (lb)</th>
  <th>Factor Op.</th>
  <th>TM Op.</th>
  <th>TM Acum.</th>
  <th style="text-align:left">Notas</th>
</tr>
</thead>
<tbody>
@foreach($cable->operaciones as $i => $op)
<tr class="{{ $op->cod === '14' ? 'cut-row' : '' }}">
  <td>{{ $i + 1 }}</td>
  <td>{{ $op->fecha->format('d/m/Y') }}</td>
  <td style="font-weight:bold">{{ $op->cod }}</td>
  <td style="text-align:left;font-size:6.5px">{{ $op->tipo_operacion }}</td>
  <td>{{ number_format($op->prof_inicial_ft, 0) }}</td>
  <td>{{ number_format($op->prof_final_ft, 0) }}</td>
  <td>{{ number_format($op->densidad_lodo_ppg, 1) }}</td>
  <td>{{ number_format($op->peso_dp_lb_ft, 2) }}</td>
  <td>{{ number_format($op->peso_bha_lb_ft, 2) }}</td>
  <td>{{ number_format($op->long_bha_ft, 0) }}</td>
  <td>{{ number_format($op->peso_bloque_lb, 0) }}</td>
  <td>{{ number_format($op->factor_operacion, 2) }}</td>
  <td style="font-weight:bold;color:#0e7490">{{ number_format($op->tm_operacion, 4) }}</td>
  <td style="font-weight:bold">{{ number_format($op->tm_acumulado, 4) }}</td>
  <td style="text-align:left;font-size:6px">{{ $op->notas }}</td>
</tr>
@endforeach
<tr class="total-row">
  <td colspan="12" style="text-align:right">TOTAL TM</td>
  <td>{{ number_format($cable->operaciones->sum('tm_operacion'), 4) }}</td>
  <td>{{ number_format($tmAcum, 4) }}</td>
  <td></td>
</tr>
</tbody>
</table>

{{-- Cortes --}}
@if($cable->cortes->count())
<div class="section-title">✂ HISTORIAL DE CORTES</div>
<table class="ops">
<thead>
<tr>
  <th>Fecha</th>
  <th>Pies Cortados (ft)</th>
  <th>TM al Corte</th>
  <th style="text-align:left">Motivo</th>
</tr>
</thead>
<tbody>
@foreach($cable->cortes as $c)
<tr>
  <td>{{ $c->fecha->format('d/m/Y') }}</td>
  <td>{{ number_format($c->ft_cortados, 1) }}</td>
  <td style="font-weight:bold">{{ number_format($c->tm_al_corte, 2) }}</td>
  <td style="text-align:left">{{ $c->motivo ?? '—' }}</td>
</tr>
@endforeach
</tbody>
</table>
@endif

<p class="footer">
  GRS — General Rigs Services S.A. &nbsp;|&nbsp; Tel: (+571) 876-7422 &nbsp;|&nbsp;
  operaciones@grssa.com &nbsp;|&nbsp; www.grssa.com &nbsp;|&nbsp;
  Generado: {{ now()->format('d/m/Y H:i') }}
  @if($cable->activo) &nbsp;|&nbsp; Cable ACTIVO @else &nbsp;|&nbsp; Cable ARCHIVADO @endif
</p>
</body>
</html>
