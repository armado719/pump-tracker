<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pump Tracker GRS') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0b1622">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin:0;padding:0;font-family:'Figtree',sans-serif;background:#0b1622;">

<div style="display:flex;min-height:100vh;">

    {{-- ── PANEL IZQUIERDO — Branding ────────────────────────────────── --}}
    <div class="hidden lg:flex" style="flex:1;flex-direction:column;justify-content:center;align-items:center;
         background:linear-gradient(160deg,#0b1f16 0%,#0b1622 50%,#071018 100%);
         position:relative;overflow:hidden;padding:3rem;">

        {{-- Círculos decorativos de fondo --}}
        <div style="position:absolute;width:400px;height:400px;border-radius:50%;
             border:1px solid rgba(74,222,128,0.08);top:-80px;left:-80px;"></div>
        <div style="position:absolute;width:600px;height:600px;border-radius:50%;
             border:1px solid rgba(74,222,128,0.05);top:-180px;left:-180px;"></div>
        <div style="position:absolute;width:300px;height:300px;border-radius:50%;
             border:1px solid rgba(6,182,212,0.08);bottom:-60px;right:-60px;"></div>

        {{-- Logo GRS --}}
        <div style="position:relative;z-index:1;text-align:center;">
            <img src="/images/grs-logo.png" alt="GRS"
                 style="width:140px;height:140px;border-radius:50%;object-fit:cover;
                        box-shadow:0 0 0 3px rgba(74,222,128,0.3),0 0 40px rgba(74,222,128,0.15);
                        margin-bottom:2rem;">

            <h1 style="color:#ffffff;font-size:2.5rem;font-weight:700;letter-spacing:0.15em;
                        margin:0 0 0.5rem;">GRS</h1>
            <p style="color:#4a8a9e;font-size:1rem;letter-spacing:0.05em;margin:0 0 3rem;">
                General Rigs Services S.A.S.
            </p>

            {{-- Divisor --}}
            <div style="width:60px;height:2px;background:linear-gradient(90deg,transparent,#4ade80,transparent);
                         margin:0 auto 3rem;"></div>

            {{-- Descripción --}}
            <p style="color:#6b9e82;font-size:1.05rem;max-width:340px;line-height:1.7;margin:0 auto 3rem;">
                Sistema de seguimiento operacional de equipos de perforación, bombas y cable TM.
            </p>

            {{-- Stats decorativos --}}
            <div style="display:flex;gap:2.5rem;justify-content:center;">
                <div style="text-align:center;">
                    <div style="color:#4ade80;font-size:1.75rem;font-weight:700;">24/7</div>
                    <div style="color:#4a8a9e;font-size:0.75rem;letter-spacing:0.08em;text-transform:uppercase;">Monitoreo</div>
                </div>
                <div style="width:1px;background:rgba(74,222,128,0.15);"></div>
                <div style="text-align:center;">
                    <div style="color:#4ade80;font-size:1.75rem;font-weight:700;">100%</div>
                    <div style="color:#4a8a9e;font-size:0.75rem;letter-spacing:0.08em;text-transform:uppercase;">Trazabilidad</div>
                </div>
                <div style="width:1px;background:rgba(74,222,128,0.15);"></div>
                <div style="text-align:center;">
                    <div style="color:#4ade80;font-size:1.75rem;font-weight:700;">TM</div>
                    <div style="color:#4a8a9e;font-size:0.75rem;letter-spacing:0.08em;text-transform:uppercase;">Cable Track</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PANEL DERECHO — Formulario ───────────────────────────────── --}}
    <div style="width:100%;max-width:480px;display:flex;flex-direction:column;
                justify-content:center;align-items:center;padding:2rem;
                background:#0f1f2e;border-left:1px solid rgba(74,222,128,0.1);">

        {{-- Logo mobile (solo visible en pantallas pequeñas) --}}
        <div class="lg:hidden" style="text-align:center;margin-bottom:2rem;">
            <img src="/images/grs-logo.png" alt="GRS"
                 style="width:90px;height:90px;border-radius:50%;object-fit:cover;
                        box-shadow:0 0 0 2px rgba(74,222,128,0.3);margin-bottom:1rem;">
            <div style="color:#ffffff;font-size:1.5rem;font-weight:700;letter-spacing:0.1em;">GRS</div>
            <div style="color:#4a8a9e;font-size:0.85rem;">General Rigs Services S.A.S.</div>
        </div>

        <div style="width:100%;max-width:380px;">
            <h2 style="color:#ffffff;font-size:1.6rem;font-weight:700;margin:0 0 0.4rem;">
                Bienvenido
            </h2>
            <p style="color:#4a8a9e;font-size:0.9rem;margin:0 0 2rem;">
                Ingresa tus credenciales para continuar
            </p>

            {{ $slot }}

            <p style="color:#2a4a3a;font-size:0.75rem;text-align:center;margin-top:2.5rem;">
                Pump Tracker GRS © {{ date('Y') }}
            </p>
        </div>
    </div>

</div>
</body>
</html>
