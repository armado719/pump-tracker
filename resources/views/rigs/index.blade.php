@extends('layouts.app')
@section('title', 'Rigs')
@section('header', 'Rigs de Perforación')

@section('content')
<div class="pt-4">
    <div class="flex items-center justify-between mb-5">
        <p class="text-gray-500 text-sm">{{ $rigs->count() }} rig(s) configurados</p>
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('rigs.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-lg font-medium transition">
            + Nuevo Rig
        </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($rigs as $rig)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden">
            <div class="bg-blue-600 px-5 py-4">
                <h3 class="text-white font-bold text-lg">{{ $rig->name }}</h3>
                @if($rig->location)
                    <p class="text-blue-200 text-sm">📍 {{ $rig->location }}</p>
                @endif
            </div>
            <div class="p-5 space-y-2">
                @if($rig->manager)
                    <p class="text-sm text-gray-600"><span class="font-medium">Manager:</span> {{ $rig->manager }}</p>
                @endif
                <p class="text-sm text-gray-600"><span class="font-medium">Bombas:</span> {{ $rig->pumps_count }}</p>
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('rigs.show', $rig) }}"
                       class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm py-2 rounded-lg font-medium transition">
                        Ver detalle
                    </a>
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('rigs.edit', $rig) }}"
                       class="px-3 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm py-2 rounded-lg transition">
                        ✏️
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
