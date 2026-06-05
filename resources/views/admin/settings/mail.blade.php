@extends('layouts.app')
@section('title', 'Configuración de Correo')

@section('content')
<div class="pt-4 max-w-xl mx-auto space-y-5">

    <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver al Dashboard</a>

    {{-- Estado actual --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-semibold text-gray-800 text-sm">Estado del Servidor de Correo</h2>
            @php $mailerActual = $settings['mail_mailer'] ?? config('mail.default', 'log'); @endphp
            <span class="px-2 py-0.5 rounded-full text-xs font-bold border
                {{ $mailerActual === 'log' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-green-100 text-green-700 border-green-200' }}">
                {{ strtoupper($mailerActual) }}
            </span>
        </div>
        <p class="text-xs text-gray-500">
            Remitente:
            <strong>{{ $settings['mail_from_address'] ?? config('mail.from.address', '—') }}</strong>
            &mdash; "{{ $settings['mail_from_name'] ?? config('mail.from.name', '—') }}"
        </p>
        @if($mailerActual === 'log')
        <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
            ⚠ Modo <strong>LOG</strong> activo — los correos se escriben en el log de Laravel y no llegan
            a ninguna bandeja de entrada. Configura SMTP para enviar correos reales.
        </div>
        @endif
    </div>

    {{-- Formulario --}}
    <form method="POST" action="{{ route('admin.settings.mail.update') }}"
          class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        @csrf @method('PUT')

        <h3 class="font-semibold text-gray-800 border-b pb-3">Parámetros SMTP</h3>

        <div class="grid grid-cols-2 gap-4">

            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Driver / Mailer *</label>
                <select name="mail_mailer" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    @foreach([
                        'smtp'     => 'SMTP (recomendado)',
                        'log'      => 'Log (solo desarrollo)',
                        'mailgun'  => 'Mailgun',
                        'ses'      => 'Amazon SES',
                        'sendmail' => 'Sendmail',
                    ] as $val => $label)
                        <option value="{{ $val }}" {{ ($settings['mail_mailer'] ?? config('mail.default', 'log')) === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Host SMTP</label>
                <input type="text" name="mail_host"
                       value="{{ old('mail_host', $settings['mail_host'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="smtp.gmail.com">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Puerto</label>
                <input type="number" name="mail_port"
                       value="{{ old('mail_port', $settings['mail_port'] ?? 587) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="587">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Usuario / Email</label>
                <input type="text" name="mail_username"
                       value="{{ old('mail_username', $settings['mail_username'] ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="tu@correo.com">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">
                    Contraseña / App Password
                    <span class="normal-case text-gray-400 font-normal">(vacío = no cambiar)</span>
                </label>
                <input type="password" name="mail_password" autocomplete="new-password"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="{{ isset($settings['mail_password']) && $settings['mail_password'] ? '••••••••' : 'Sin configurar' }}">
            </div>

            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Cifrado</label>
                <select name="mail_encryption"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Sin cifrado</option>
                    <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS (recomendado — puerto 587)</option>
                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl'  ? 'selected' : '' }}>SSL (puerto 465)</option>
                </select>
            </div>

            <div class="col-span-2 border-t pt-3">
                <p class="text-xs font-semibold text-gray-600 uppercase mb-3">Remitente</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Email remitente *</label>
                <input type="email" name="mail_from_address" required
                       value="{{ old('mail_from_address', $settings['mail_from_address'] ?? config('mail.from.address', '')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="operaciones@grssa.com">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Nombre remitente *</label>
                <input type="text" name="mail_from_name" required
                       value="{{ old('mail_from_name', $settings['mail_from_name'] ?? config('mail.from.name', '')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="GRS — General Rigs Services S.A.S.">
            </div>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium text-sm transition">
                Guardar configuración
            </button>
        </div>
    </form>

    {{-- Prueba de correo --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-1 text-sm">Probar configuración</h3>
        <p class="text-xs text-gray-500 mb-3">
            Envía un correo de prueba a <strong>{{ auth()->user()->email }}</strong> con la configuración actual (guardada).
        </p>
        <form method="POST" action="{{ route('admin.settings.mail.test') }}">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Enviar correo de prueba
            </button>
        </form>
    </div>

    {{-- Ayuda --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-xs text-blue-800">
        <p class="font-semibold mb-2">Configuraciones comunes:</p>
        <ul class="space-y-1 text-blue-700">
            <li><strong>Gmail:</strong> smtp.gmail.com | Puerto 587 | TLS | Usa una "Contraseña de Aplicación" (no la contraseña normal)</li>
            <li><strong>Outlook / Office 365:</strong> smtp.office365.com | Puerto 587 | TLS</li>
            <li><strong>Mailgun:</strong> smtp.mailgun.org | Puerto 587 | TLS</li>
            <li><strong>Amazon SES:</strong> email-smtp.us-east-1.amazonaws.com | Puerto 587 | TLS</li>
        </ul>
    </div>

</div>
@endsection
