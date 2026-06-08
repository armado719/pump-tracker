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

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Figtree', sans-serif;
            min-height: 100vh;
            overflow: hidden;
            background: #071018;
        }

        /* ── Fondo con efecto Ken Burns (zoom lento) ── */
        .bg-image {
            position: fixed;
            inset: 0;
            background-image: url('/images/grs-bg-login.jpg');
            background-size: cover;
            background-position: center right;
            animation: kenBurns 20s ease-in-out infinite alternate;
            transform-origin: center;
        }

        @keyframes kenBurns {
            0%   { transform: scale(1)    translate(0, 0); }
            33%  { transform: scale(1.06) translate(-1%, 1%); }
            66%  { transform: scale(1.04) translate(1%, -0.5%); }
            100% { transform: scale(1.08) translate(-0.5%, 0.5%); }
        }

        /* ── Overlay gradiente: oscuro izquierda, transparente derecha ── */
        .bg-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(
                105deg,
                rgba(5, 15, 10, 0.92) 0%,
                rgba(7, 20, 14, 0.85) 35%,
                rgba(10, 25, 18, 0.55) 60%,
                rgba(0, 0, 0, 0.15) 100%
            );
        }

        /* ── Partículas flotantes ── */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(74, 222, 128, 0.15);
            animation: floatUp linear infinite;
        }

        @keyframes floatUp {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 0.4; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* ── Contenedor principal ── */
        .login-wrapper {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem;
        }

        /* ── Card del formulario ── */
        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(8, 20, 15, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(74, 222, 128, 0.15);
            border-radius: 1.25rem;
            padding: 2.5rem;
            box-shadow:
                0 0 0 1px rgba(74, 222, 128, 0.05),
                0 25px 60px rgba(0, 0, 0, 0.6),
                0 0 80px rgba(74, 222, 128, 0.05);
            animation: slideIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes slideIn {
            0%   { opacity: 0; transform: translateX(-40px) scale(0.97); }
            100% { opacity: 1; transform: translateX(0)    scale(1); }
        }

        /* ── Logo ── */
        .logo-wrap {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeDown 0.7s 0.2s ease both;
        }

        .logo-img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(74, 222, 128, 0.4);
            box-shadow:
                0 0 0 4px rgba(74, 222, 128, 0.08),
                0 0 30px rgba(74, 222, 128, 0.2);
            margin-bottom: 1rem;
            animation: logoPulse 3s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(74,222,128,0.08), 0 0 30px rgba(74,222,128,0.2); }
            50%       { box-shadow: 0 0 0 6px rgba(74,222,128,0.15), 0 0 50px rgba(74,222,128,0.35); }
        }

        @keyframes fadeDown {
            0%   { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* ── Línea decorativa ── */
        .divider {
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #4ade80, transparent);
            margin: 0.75rem auto 0;
            animation: expandLine 1s 0.5s ease both;
        }

        @keyframes expandLine {
            0%   { width: 0; opacity: 0; }
            100% { width: 50px; opacity: 1; }
        }

        /* ── Campos input ── */
        .field { margin-bottom: 1.25rem; animation: fadeUp 0.5s ease both; }
        .field:nth-child(1) { animation-delay: 0.3s; }
        .field:nth-child(2) { animation-delay: 0.4s; }
        .field:nth-child(3) { animation-delay: 0.5s; }
        .field:nth-child(4) { animation-delay: 0.6s; }

        @keyframes fadeUp {
            0%   { opacity: 0; transform: translateY(15px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .field label {
            display: block;
            color: #6b9e82;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .field input {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 0.6rem;
            font-size: 0.95rem;
            background: rgba(0, 20, 12, 0.6);
            border: 1px solid rgba(74, 222, 128, 0.2);
            color: #f0ede8;
            outline: none;
            transition: border-color 0.3s, box-shadow 0.3s, background 0.3s;
        }

        .field input:focus {
            border-color: rgba(74, 222, 128, 0.6);
            background: rgba(0, 20, 12, 0.8);
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.1), 0 0 20px rgba(74, 222, 128, 0.05);
        }

        /* ── Botón submit ── */
        .btn-login {
            width: 100%;
            padding: 0.9rem;
            border-radius: 0.6rem;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, #16a34a 0%, #4ade80 100%);
            color: #071018;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            animation: fadeUp 0.5s 0.65s ease both;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 40%;
            height: 200%;
            background: rgba(255,255,255,0.25);
            transform: skewX(-20deg);
            transition: left 0.5s ease;
        }

        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(74,222,128,0.35); }
        .btn-login:hover::before { left: 140%; }
        .btn-login:active { transform: translateY(0); }

        /* ── Footer ── */
        .card-footer {
            text-align: center;
            margin-top: 2rem;
            color: rgba(74, 222, 128, 0.2);
            font-size: 0.72rem;
            letter-spacing: 0.05em;
            animation: fadeUp 0.5s 0.8s ease both;
        }

        /* ── Línea animada inferior de la card ── */
        .card-glow {
            height: 2px;
            background: linear-gradient(90deg, transparent, #4ade80, transparent);
            border-radius: 0 0 1.25rem 1.25rem;
            animation: glowPulse 2.5s ease-in-out infinite;
        }

        @keyframes glowPulse {
            0%, 100% { opacity: 0.3; }
            50%       { opacity: 1; }
        }
    </style>
</head>
<body>

    {{-- Fondo con Ken Burns --}}
    <div class="bg-image"></div>
    <div class="bg-overlay"></div>

    {{-- Partículas flotantes --}}
    <div class="particles" id="particles"></div>

    {{-- Contenido --}}
    <div class="login-wrapper">
        <div class="login-card">

            {{-- Logo --}}
            <div class="logo-wrap">
                <img src="/images/grs-logo.png" alt="GRS" class="logo-img">
                <div style="color:#ffffff;font-size:1.4rem;font-weight:700;letter-spacing:0.15em;">GRS</div>
                <div style="color:#4a8a9e;font-size:0.8rem;letter-spacing:0.04em;">General Rigs Services S.A.S.</div>
                <div class="divider"></div>
            </div>

            {{-- Formulario --}}
            {{ $slot }}

            {{-- Footer --}}
            <div class="card-footer">Pump Tracker GRS © {{ date('Y') }}</div>
        </div>

        <div class="card-glow" style="position:absolute;bottom:0;left:0;right:0;max-width:420px;"></div>
    </div>

    <script>
        // Generar partículas flotantes
        (function() {
            const container = document.getElementById('particles');
            for (let i = 0; i < 18; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                const size = Math.random() * 6 + 2;
                p.style.cssText = `
                    width:${size}px; height:${size}px;
                    left:${Math.random() * 45}%;
                    animation-duration:${Math.random() * 12 + 10}s;
                    animation-delay:${Math.random() * 10}s;
                    opacity:${Math.random() * 0.4 + 0.1};
                `;
                container.appendChild(p);
            }
        })();
    </script>

</body>
</html>
