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
                    <th class="px-4 py-3 text-center">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                @php $isMe = $user->id === auth()->id(); @endphp
                <tr class="hover:bg-gray-50 {{ $isMe ? 'bg-blue-50' : (!$user->active ? 'opacity-50' : '') }}">
                    <td class="px-4 py-3 font-medium">
                        {{ $user->name }}
                        @if($isMe)
                            <span class="text-xs text-blue-500 ml-1">(tú)</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $roleColors = [
                                'admin'       => 'bg-purple-100 text-purple-700 border-purple-200',
                                'rig_manager' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'supervisor'  => 'bg-green-100 text-green-700 border-green-200',
                            ];
                            $roleLabels = [
                                'admin'       => 'Admin',
                                'rig_manager' => 'Rig Manager',
                                'supervisor'  => 'Supervisor',
                            ];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium border {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                            {{ $roleLabels[$user->role] ?? $user->role }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $user->rig?->name ?? '— todos —' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($user->active ?? true)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Activo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactivo
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:underline text-xs">✏️ Editar</a>

                            @if(!$isMe)
                                {{-- Toggle activo/inactivo --}}
                                <form method="POST" action="{{ route('users.toggle', $user) }}">
                                    @csrf @method('PATCH')
                                    @if($user->active ?? true)
                                        <button type="submit"
                                                onclick="return confirm('¿Desactivar a {{ $user->name }}? No podrá iniciar sesión.')"
                                                class="text-orange-500 hover:underline text-xs">
                                            ⏸ Desactivar
                                        </button>
                                    @else
                                        <button type="submit" class="text-green-600 hover:underline text-xs">
                                            ▶ Activar
                                        </button>
                                    @endif
                                </form>

                                {{-- Eliminar --}}
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirm('¿Eliminar permanentemente a {{ $user->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline text-xs">🗑 Eliminar</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
