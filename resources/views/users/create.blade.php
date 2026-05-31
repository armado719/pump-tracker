@extends('layouts.app')
@section('title', 'Nuevo Usuario')

@section('content')
<div class="pt-4 max-w-lg mx-auto space-y-5">

    <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-semibold text-gray-800 mb-5 text-base">Crear nuevo usuario</h2>

        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Nombre completo *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Contraseña *</label>
                <input type="password" name="password" required minlength="6"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Confirmar contraseña *</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Rol *</label>
                <select name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="encuellador" {{ old('role') === 'encuellador' ? 'selected' : '' }}>Encuellador</option>
                    <option value="supervisor"  {{ old('role') === 'supervisor'  ? 'selected' : '' }}>Supervisor</option>
                    <option value="rig_manager" {{ old('role') === 'rig_manager' ? 'selected' : '' }}>Rig Manager</option>
                    <option value="admin"       {{ old('role') === 'admin'       ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Rig asignado</label>
                <select name="rig_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">— Todos los rigs —</option>
                    @foreach($rigs as $rig)
                        <option value="{{ $rig->id }}" {{ old('rig_id') == $rig->id ? 'selected' : '' }}>{{ $rig->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Deja vacío para que el usuario vea todos los rigs.</p>
            </div>

            <div class="pt-2 flex justify-between">
                <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium text-sm transition">
                    Crear usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
