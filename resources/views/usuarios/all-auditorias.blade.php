@extends('layouts.app')

@section('ruta-actual', 'Auditorías')
@section('ruta-anterior', 'Gestión de usuarios')
@section('url', '/gestion_usuarios')

@section('contenido')
    <header class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Registro de Actividades</h2>
            <p class="text-gray-500 text-sm">Historial de todas las acciones realizadas en el sistema</p>
        </div>
    </header>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow-sm mb-6 p-4">
        <form method="GET" action="{{ route('auditoria.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Búsqueda --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}"
                        placeholder="Buscar en descripción..."
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Usuario --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                    <select name="usuario"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}"
                                {{ ($filtros['usuario'] ?? '') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Módulo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Módulo</label>
                    <select name="modulo"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos</option>
                        @foreach ($modulos as $modulo)
                            <option value="{{ $modulo }}"
                                {{ ($filtros['modulo'] ?? '') == $modulo ? 'selected' : '' }}>
                                {{ ucfirst($modulo) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Acción --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
                    <select name="accion"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todas</option>
                        @foreach ($acciones as $accion)
                            <option value="{{ $accion }}"
                                {{ ($filtros['accion'] ?? '') == $accion ? 'selected' : '' }}>
                                {{ ucfirst($accion) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fecha Desde --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Fecha Hasta --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                {{-- Botones --}}
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filtrar
                    </button>
                    <a href="{{ route('auditoria.index') }}"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                        Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Lista de auditorías --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Actividades Recientes</h3>
            <span class="text-sm text-gray-500">{{ $auditorias->total() }} registros</span>
        </div>

        @if ($auditorias->isEmpty())
            <div class="p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="text-gray-500">No se encontraron registros de actividad</p>
                <p class="text-gray-400 text-sm">Intenta ajustar los filtros de búsqueda</p>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach ($auditorias as $auditoria)
                    <div class="p-4 hover:bg-gray-50 transition-colors" x-data="{ open: false }">
                        <div class="flex items-start gap-4">
                            {{-- Icono de acción --}}
                            <div class="flex-shrink-0">
                                @php
                                    $iconColors = [
                                        'crear' => 'bg-green-100 text-green-600',
                                        'actualizar' => 'bg-blue-100 text-blue-600',
                                        'eliminar' => 'bg-red-100 text-red-600',
                                        'login' => 'bg-indigo-100 text-indigo-600',
                                        'logout' => 'bg-gray-100 text-gray-600',
                                        'anular' => 'bg-orange-100 text-orange-600',
                                    ];
                                    $iconClass = $iconColors[$auditoria->accion] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <div class="w-10 h-10 rounded-full {{ $iconClass }} flex items-center justify-center">
                                    @switch($auditoria->accion)
                                        @case('crear')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        @break

                                        @case('actualizar')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        @break

                                        @case('eliminar')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        @break

                                        @case('login')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                        @break

                                        @case('logout')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                        @break

                                        @default
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                    @endswitch
                                </div>
                            </div>

                            {{-- Contenido --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    {!! $auditoria->accion_badge !!}
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                        {{ $auditoria->entidad_legible }}
                                    </span>
                                    @if ($auditoria->modulo)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                            {{ ucfirst($auditoria->modulo) }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-gray-900 font-medium">
                                    {{ $auditoria->descripcion ?? 'Sin descripción' }}
                                </p>

                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $auditoria->user->name ?? 'Sistema' }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $auditoria->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    @if ($auditoria->ip_address)
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                            </svg>
                                            {{ $auditoria->ip_address }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Botón para ver detalles --}}
                                @if ($auditoria->datos_anteriores || $auditoria->datos_nuevos)
                                    <button @click="open = !open"
                                        class="mt-2 text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform"
                                            :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                        Ver detalles del cambio
                                    </button>
                                @endif
                            </div>

                            {{-- Tiempo relativo --}}
                            <div class="flex-shrink-0 text-right">
                                <span class="text-sm text-gray-400">{{ $auditoria->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Panel de detalles expandible --}}
                        @if ($auditoria->datos_anteriores || $auditoria->datos_nuevos)
                            <div x-show="open" x-collapse class="mt-4 ml-14">
                                <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if ($auditoria->datos_anteriores)
                                        <div>
                                            <h5 class="text-sm font-semibold text-gray-600 mb-2 flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                                Datos Anteriores
                                            </h5>
                                            <div class="bg-white rounded-lg p-3 text-sm space-y-1">
                                                @foreach ($auditoria->datos_anteriores as $key => $value)
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">{{ $key }}:</span>
                                                        <span class="text-gray-900 font-medium">
                                                            @if (is_array($value))
                                                                {{ json_encode($value) }}
                                                            @else
                                                                {{ $value ?? 'null' }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($auditoria->datos_nuevos)
                                        <div>
                                            <h5 class="text-sm font-semibold text-gray-600 mb-2 flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                                Datos Nuevos
                                            </h5>
                                            <div class="bg-white rounded-lg p-3 text-sm space-y-1">
                                                @foreach ($auditoria->datos_nuevos as $key => $value)
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-500">{{ $key }}:</span>
                                                        <span class="text-gray-900 font-medium">
                                                            @if (is_array($value))
                                                                {{ json_encode($value) }}
                                                            @else
                                                                {{ $value ?? 'null' }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Paginación --}}
            <div class="p-4 border-t border-gray-200">
                {{ $auditorias->links() }}
            </div>
        @endif
    </div>
@endsection
