@extends('layouts.app')
@section('title', 'Mi Perfil')

@section('content')
<div class="pt-4 max-w-xl mx-auto space-y-5">

    {{-- ── Información del perfil ──────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-semibold text-gray-800 mb-1">Información del perfil</h2>
        <p class="text-xs text-gray-400 mb-5">Actualiza tu nombre y correo electrónico.</p>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Nombre *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                @foreach($errors->get('name') as $msg)
                    <p class="text-red-500 text-xs mt-1">{{ $msg }}</p>
                @endforeach
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                @foreach($errors->get('email') as $msg)
                    <p class="text-red-500 text-xs mt-1">{{ $msg }}</p>
                @endforeach
            </div>

            <div class="flex items-center justify-between pt-1">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    Guardar información
                </button>
                @if(session('status') === 'profile-updated')
                    <span class="text-green-600 text-sm font-medium">✓ Guardado</span>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Cambiar contraseña ───────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-semibold text-gray-800 mb-1">Cambiar contraseña</h2>
        <p class="text-xs text-gray-400 mb-5">Usa una contraseña larga y segura para proteger tu cuenta.</p>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Contraseña actual *</label>
                <input type="password" name="current_password" autocomplete="current-password" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                @foreach($errors->updatePassword->get('current_password') as $msg)
                    <p class="text-red-500 text-xs mt-1">{{ $msg }}</p>
                @endforeach
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Nueva contraseña *</label>
                <input type="password" name="password" autocomplete="new-password" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                @foreach($errors->updatePassword->get('password') as $msg)
                    <p class="text-red-500 text-xs mt-1">{{ $msg }}</p>
                @endforeach
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Confirmar nueva contraseña *</label>
                <input type="password" name="password_confirmation" autocomplete="new-password" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                @foreach($errors->updatePassword->get('password_confirmation') as $msg)
                    <p class="text-red-500 text-xs mt-1">{{ $msg }}</p>
                @endforeach
            </div>

            <div class="flex items-center justify-between pt-1">
                <button type="submit"
                        class="bg-gray-800 hover:bg-gray-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
                    Cambiar contraseña
                </button>
                @if(session('status') === 'password-updated')
                    <span class="text-green-600 text-sm font-medium">✓ Contraseña actualizada</span>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Info de la cuenta ───────────────────────────────────────────── --}}
    <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 text-xs text-gray-500">
        <p class="font-semibold text-gray-700 mb-1">Información de la cuenta</p>
        <p>Rol: <strong>{{ ['admin'=>'Administrador','rig_manager'=>'Rig Manager','supervisor'=>'Supervisor'][$user->role] ?? $user->role }}</strong></p>
        <p>Rig asignado: <strong>{{ $user->rig?->name ?? '— todos los rigs —' }}</strong></p>
        <p>Cuenta activa desde: <strong>{{ $user->created_at?->format('d/m/Y') }}</strong></p>
    </div>

</div>
@endsection
