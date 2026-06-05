<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 14px; color: #111; background: #f9fafb; margin: 0; padding: 0; }
  .wrapper { max-width: 640px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
  .header { background: #0b1622; padding: 20px 24px; }
  .header h1 { color: #fff; font-size: 16px; margin: 0; }
  .header p { color: #6b9e82; font-size: 12px; margin: 4px 0 0 0; }
  .body { padding: 24px; }
  .section-title { font-size: 13px; font-weight: bold; text-transform: uppercase;
                   letter-spacing: 0.5px; margin: 20px 0 8px 0; padding-bottom: 4px;
                   border-bottom: 2px solid #e5e7eb; }
  .section-title.pump { color: #166534; border-color: #bbf7d0; }
  .section-title.cable { color: #0e7490; border-color: #a5f3fc; }
  table { width: 100%; border-collapse: collapse; font-size: 12px; }
  th { background: #f3f4f6; color: #374151; padding: 6px 8px; text-align: left;
       font-size: 11px; text-transform: uppercase; border: 1px solid #e5e7eb; }
  td { padding: 6px 8px; border: 1px solid #e5e7eb; }
  .badge-critical { background: #fee2e2; color: #dc2626; padding: 2px 6px; border-radius: 9999px;
                    font-size: 11px; font-weight: bold; }
  .badge-warning  { background: #fef3c7; color: #d97706; padding: 2px 6px; border-radius: 9999px;
                    font-size: 11px; font-weight: bold; }
  .footer { background: #f9fafb; padding: 16px 24px; font-size: 11px; color: #6b7280;
            text-align: center; border-top: 1px solid #e5e7eb; }
  .no-alerts { background: #f0fdf4; color: #166534; padding: 10px 12px; border-radius: 6px;
               font-size: 12px; border: 1px solid #bbf7d0; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>GRS — General Rigs Services S.A.</h1>
    <p>Reporte de Alertas — {{ $rigName }} — {{ now()->format('d/m/Y H:i') }}</p>
  </div>

  <div class="body">
    <p style="color:#374151;font-size:13px;">
      Se han detectado las siguientes alertas activas en el equipo <strong>{{ $rigName }}</strong>. Tome las acciones necesarias.
    </p>

    {{-- ALERTAS BOMBAS --}}
    <div class="section-title pump">Bombas — Componentes</div>
    @if(count($alertasBombas) === 0)
      <div class="no-alerts">✅ Sin alertas activas en componentes.</div>
    @else
    <table>
      <thead>
        <tr>
          <th>Estado</th>
          <th>Bomba</th>
          <th>Conjunto</th>
          <th>Componente</th>
          <th>Horas acum.</th>
          <th>Límite crítico</th>
        </tr>
      </thead>
      <tbody>
        @foreach($alertasBombas as $a)
        <tr>
          <td><span class="{{ $a['status'] === 'critical' ? 'badge-critical' : 'badge-warning' }}">
            {{ $a['status'] === 'critical' ? '🔴 CRÍTICO' : '⚠ ALERTA' }}
          </span></td>
          <td>{{ $a['pump'] }}</td>
          <td>{{ $a['assembly'] }}</td>
          <td>{{ $a['component'] }}</td>
          <td style="font-weight:bold">{{ number_format($a['hours'], 2) }}h</td>
          <td style="color:#6b7280">{{ $a['critical'] }}h</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif

    {{-- ALERTAS CABLE TM --}}
    <div class="section-title cable">Cable TM</div>
    @if(count($alertasCables) === 0)
      <div class="no-alerts" style="background:#ecfeff;color:#0e7490;border-color:#a5f3fc;">
        ✅ Cable dentro de límites normales.
      </div>
    @else
    <table>
      <thead>
        <tr>
          <th>Estado</th>
          <th>Serial</th>
          <th>TM Acumulado</th>
          <th>TM Máximo</th>
          <th>% Vida útil</th>
        </tr>
      </thead>
      <tbody>
        @foreach($alertasCables as $c)
        <tr>
          <td><span class="{{ $c['status'] === 'critical' ? 'badge-critical' : 'badge-warning' }}">
            {{ $c['status'] === 'critical' ? '🔴 CRÍTICO' : '⚠ ALERTA' }}
          </span></td>
          <td style="font-weight:bold">{{ $c['serial'] }}</td>
          <td>{{ number_format($c['tmAcum'], 2) }} TM</td>
          <td>{{ number_format($c['tmMax'], 0) }} TM</td>
          <td style="font-weight:bold">{{ $c['pct'] }}%</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>

  <div class="footer">
    <strong>GRS — General Rigs Services S.A.</strong><br>
    Tel: (+571) 876-7422 &nbsp;|&nbsp;
    <a href="mailto:operaciones@grssa.com" style="color:#6b7280;">operaciones@grssa.com</a> &nbsp;|&nbsp;
    <a href="https://www.grssa.com" style="color:#6b7280;">www.grssa.com</a><br>
    <span style="color:#9ca3af;">Pump Tracker — {{ now()->format('d/m/Y H:i') }} — Mensaje automático, no responder.</span>
  </div>
</div>
</body>
</html>
