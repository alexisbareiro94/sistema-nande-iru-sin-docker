@extends('layouts.app')

@section('titulo', 'Servicios en Proceso')

@section('ruta-actual', 'Servicios en Proceso')

@section('contenido')
    <header class="flex justify-between md:flex-row md:items-center mb-6 gap-4">
        <div class="flex items-center gap-4">
            <div>
                <h2 class="text-lg md:text-2xl px-2 md:px-0 font-bold text-gray-800">Servicios en Proceso</h2>
                <p class="text-gray-500 text-sm">Gestión de servicios activos del taller</p>
            </div>
        </div>
        <button id="btn-nuevo-servicio"
            class="bg-gray-800 hover:bg-gray-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-200 hover:shadow-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Agregar Servicio
        </button>
    </header>

    {{-- Estadísticas rápidas --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-gray-500 text-sm">Pendientes</p>
            <p class="text-2xl font-bold text-gray-800">{{ $servicios->where('estado', 'pendiente')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">En Proceso</p>
            <p class="text-2xl font-bold text-gray-800">{{ $servicios->where('estado', 'en_proceso')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Total Activos</p>
            <p class="text-2xl font-bold text-gray-800">{{ $servicios->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-gray-500">
            <p class="text-gray-500 text-sm">Mecánicos Disponibles</p>
            <p class="text-2xl font-bold text-gray-800">{{ $mecanicos->count() }}</p>
        </div>
    </div>

    {{-- Lista de servicios --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Servicios Activos</h3>
        </div>

        @if ($servicios->isEmpty())
            <div class="p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500">No hay servicios activos</p>
                <p class="text-gray-400 text-sm">Haz clic en "Agregar Servicio" para comenzar</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vehículo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mecánico</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($servicios as $servicio)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">{{ $servicio->codigo }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($servicio->vehiculo)
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $servicio->vehiculo->patente }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $servicio->vehiculo->marca }}
                                                {{ $servicio->vehiculo->modelo }}</p>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">Sin vehículo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm text-gray-600">{{ $servicio->cliente->razon_social ?? 'Sin asignar' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm text-gray-600">{{ $servicio->mecanico->name ?? 'Sin asignar' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $servicio->estado_badge !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm text-gray-500">{{ $servicio->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('servicio.proceso.show', $servicio->id) }}"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Ver Detalle
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Modal de selección de vehículo --}}
    @include('servicio-proceso.includes.modal-seleccion')
@endsection

@section('js')
    <script src="{{ asset('js/servicio-proceso.js') }}"></script>
@endsection
