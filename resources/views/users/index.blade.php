@extends('layouts.app')
@section('title', 'Gestión de Usuarios')

@section('content')
<div class="pt-4 space-y-5">

    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500">{{ $users->count() }} usuario(s) registrado(s)</p>
        <a href="{{ route('users.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg font-medium transition">
            + Nuevo Usuario
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-center">Rol</th>
                    <th class="px-4 py-3 text-left">Rig asignado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 {{ $user->id === auth()->id() ? 'bg-blue-50' : '' }}">
                    <td class="px-4 py-3 font-medium">
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                            <span class="text-xs text-blue-500 ml-1">(tú)</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $roleColors = [
                                'admin'        => 'bg-purple-100 text-purple-700 border-purple-200',
                                'rig_manager'  => 'bg-blue-100 text-blue-700 border-blue-200',
                                'supervisor'   => 'bg-green-100 text-green-700 border-green-200',
                                'encuellador'  => 'bg-gray-100 text-gray-700 border-gray-200',
                            ];
                            $roleLabels = [
                                'admin'        => 'Admin',
                                'rig_manager'  => 'Rig Manager',
                                'supervisor'   => 'Supervisor',
                                'encuellador'  => 'Encuellador',
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                            {{ $roleLabels[$user->role] ?? $user->role }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $user->rig?->name ?? '— todos —' }}</td>
                    <td class="px-4 py-3 text-center flex items-center justify-center gap-3">
                        <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:underline text-xs">✏️ Editar</a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                              onsubmit="return confirm('¿Eliminar a {{ $user->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline text-xs">🗑 Eliminar</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
