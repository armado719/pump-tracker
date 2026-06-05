<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRS — Error del servidor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0b1622;
            color: #F0EDE8;
            font-family: ui-sans-serif, system-ui, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
        }
        .container { max-width: 480px; padding: 2rem; }
        .logo-circle {
            width: 80px; height: 80px; border-radius: 50%;
            background: #1a3528; border: 2px solid #1a6b4a;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; font-weight: 900; color: #4ade80;
            margin: 0 auto 1.5rem;
        }
        .company { font-size: 0.7rem; color: #6b9e82; letter-spacing: 0.1em; margin-bottom: 2.5rem; }
        .code {
            font-size: 5rem; font-weight: 900; color: #FF4D2E;
            font-family: ui-monospace, monospace; line-height: 1;
        }
        .title { font-size: 1.25rem; font-weight: 700; margin: 0.75rem 0 0.5rem; color: #F0EDE8; }
        .desc { font-size: 0.875rem; color: #6b9e82; line-height: 1.6; margin-bottom: 2rem; }
        .btn {
            display: inline-block;
            background: #06B6D4; color: #00111a;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 700; font-size: 0.875rem;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-sec {
            display: inline-block;
            background: transparent; color: #6b9e82;
            border: 1px solid #1a3040;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            text-decoration: none;
            margin-left: 0.75rem;
            transition: opacity 0.2s;
        }
        .btn-sec:hover { opacity: 0.75; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-circle">G</div>
        <p class="company">GENERAL RIGS SERVICES S.A.S.</p>
        <div class="code">500</div>
        <h1 class="title">Error interno del servidor</h1>
        <p class="desc">
            Ocurrió un error inesperado en el servidor.<br>
            El equipo técnico ha sido notificado. Intenta de nuevo en unos minutos.
        </p>
        <a href="{{ url('/dashboard') }}" class="btn">Ir al Dashboard</a>
        <a href="javascript:history.back()" class="btn-sec">Volver</a>
    </div>
</body>
</html>
