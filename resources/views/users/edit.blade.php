@extends('layouts.app')
@section('title', 'Editar Usuario — ' . $user->name)

@section('content')
<div class="pt-4 max-w-lg mx-auto space-y-5">

    <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver</a>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-semibold text-gray-800 mb-5 text-base">Editar usuario: {{ $user->name }}</h2>

        <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Nombre completo *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Nueva contraseña <span class="normal-case text-gray-400">(dejar vacío para no cambiar)</span></label>
                <input type="password" name="password" minlength="6"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Confirmar nueva contraseña</label>
                <input type="password" name="password_confirmation"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Rol *</label>
                <select name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="encuellador" {{ old('role', $user->role) === 'encuellador' ? 'selected' : '' }}>Encuellador</option>
                    <option value="supervisor"  {{ old('role', $user->role) === 'supervisor'  ? 'selected' : '' }}>Supervisor</option>
                    <option value="rig_manager" {{ old('role', $user->role) === 'rig_manager' ? 'selected' : '' }}>Rig Manager</option>
                    <option value="admin"       {{ old('role', $user->role) === 'admin'       ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1 uppercase">Rig asignado</label>
                <select name="rig_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">— Todos los rigs —</option>
                    @foreach($rigs as $rig)
                        <option value="{{ $rig->id }}" {{ old('rig_id', $user->rig_id) == $rig->id ? 'selected' : '' }}>{{ $rig->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2 flex justify-between">
                <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium text-sm transition">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
