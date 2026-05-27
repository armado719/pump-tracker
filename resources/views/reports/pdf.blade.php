<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 8px; margin: 10px; }
  h1 { font-size: 12px; text-align: center; margin-bottom: 4px; }
  .header-grid { display: table; width: 100%; margin-bottom: 8px; border: 1px solid #ccc; }
  .header-row { display: table-row; }
  .header-cell { display: table-cell; padding: 3px 5px; border: 1px solid #ddd; }
  .lbl { font-size: 6px; color: #666; text-transform: uppercase; display: block; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #1e40af; color: white; padding: 3px 2px; text-align: center; font-size: 7px; border: 1px solid #1e3a8a; }
  td { padding: 2px 3px; border: 1px solid #d1d5db; text-align: center; font-size: 7px; }
  tr:nth-child(even) { background: #f9fafb; }
  .highlight { background: #fef9c3 !important; }
  .critical { color: #dc2626; font-weight: bold; background: #fee2e2 !important; }
  .warning { color: #d97706; background: #fffbeb !important; }
  .total-row { background: #dbeafe !important; font-weight: bold; }
  .section-header { background: #3b82f6; color: white; text-align: center; }
</style>
</head>
<body>

<h1>CONTROL DE HORAS DE BOMBA DE LODO — {{ $pump->rig->name }} — Bomba #{{ $pump->number }}</h1>

{{-- Encabezado bomba --}}
<table style="margin-bottom:8px; font-size:8px;">
<tr>
  <td><span class="lbl">RIG</span>{{ $pump->rig->name }}</td>
  <td><span class="lbl">MARCA</span>{{ $pump->brand }}</td>
  <td><span class="lbl">MODELO</span>{{ $pump->model }}</td>
  <td><span class="lbl">SERIAL</span>{{ $pump->serial }}</td>
  <td><span class="lbl">CAMISA</span>{{ $pump->liner_diameter }}</td>
  <td><span class="lbl">ACTIVO FIJO</span>{{ $pump->active_fixed_id }}</td>
  <td><span class="lbl">H. BASE</span>{{ number_format($pump->base_accumulated_hours,2) }}h</td>
</tr>
@if($personnel)
<tr>
  <td colspan="2"><span class="lbl">RIG MANAGER</span>{{ $personnel->rig_manager }}</td>
  <td colspan="2"><span class="lbl">SUPERVISOR DÍA</span>{{ $personnel->supervisor_day }}</td>
  <td><span class="lbl">SUPERVISOR NOCHE</span>{{ $personnel->supervisor_night }}</td>
  <td><span class="lbl">ENCUELLADOR DÍA</span>{{ $personnel->encuellador_day }}</td>
  <td><span class="lbl">ENCUELLADOR NOCHE</span>{{ $personnel->encuellador_night }}</td>
</tr>
@endif
</table>

{{-- Tabla principal --}}
<table>
<thead>
<tr>
  <th rowspan="2">DÍA</th>
  <th rowspan="2">FECHA</th>
  <th rowspan="2">H. DÍA</th>
  <th rowspan="2">H. ACUM</th>
  <th rowspan="2">DAMP. PSI</th>
  {{-- Conjuntos --}}
  @foreach($pump->assemblies as $assembly)
  <th colspan="{{ $assembly->components->count() }}" class="section-header">
    CONJUNTO {{ strtoupper($assembly->position_label) }}
  </th>
  @endforeach
  <th rowspan="2">COMENTARIOS</th>
</tr>
<tr>
  @foreach($pump->assemblies as $assembly)
    @foreach($assembly->components as $comp)
    <th style="font-size:6px">{{ strtoupper($comp->type_label) }}</th>
    @endforeach
  @endforeach
</tr>
</thead>
<tbody>
@php $totalHours = 0; @endphp
@foreach($tableRows as $row)
@php $totalHours += $row['hours']; @endphp
<tr class="{{ $row['comments'] ? 'highlight' : '' }}">
  <td>{{ $row['day'] }}</td>
  <td>{{ $row['date'] }}</td>
  <td style="font-weight:bold">{{ number_format($row['hours'],2) }}</td>
  <td>{{ number_format($row['accum'],2) }}</td>
  <td>{{ $row['dampener'] }}</td>
  @foreach($pump->assemblies as $assembly)
    @foreach($assembly->components as $comp)
    @php
      $ch = $row['components'][$comp->id] ?? ['hours'=>'—','status'=>'ok'];
    @endphp
    <td class="{{ $ch['status'] === 'critical' ? 'critical' : ($ch['status'] === 'warning' ? 'warning' : '') }}">
      {{ is_numeric($ch['hours']) ? number_format($ch['hours'],2) : $ch['hours'] }}
    </td>
    @endforeach
  @endforeach
  <td style="text-align:left;font-size:6px">{{ $row['comments'] }}</td>
</tr>
@endforeach
<tr class="total-row">
  <td colspan="2">TOTAL MES</td>
  <td>{{ number_format($totalHours,2) }}</td>
  <td colspan="{{ 3 + $pump->assemblies->sum(fn($a)=>$a->components->count()) + 1 }}"></td>
</tr>
</tbody>
</table>

<p style="font-size:7px;color:#666;margin-top:8px;text-align:right">
  Generado: {{ now()->format('d/m/Y H:i') }} | {{ $year }}-{{ $month }}
</p>
</body>
</html>
