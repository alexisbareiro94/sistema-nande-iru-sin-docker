@extends('layouts.app')
@section('titulo', 'Historial - ' . $vehiculo->patente)
@section('ruta-anterior', 'Vehículos')
@section('url', route('vehiculo.index'))
@section('ruta-actual', $vehiculo->patente)

@section('contenido')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-3xl font-bold text-gray-800 bg-gray-100 px-4 py-2 rounded-lg">
                        {{ $vehiculo->patente }}
                    </span>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h2>
                        <p class="text-gray-600 text-sm">
                            {{ $vehiculo->anio ?? 'Año no registrado' }}
                            @if ($vehiculo->color)
                                • {{ $vehiculo->color }}
                            @endif
                            @if ($vehiculo->kilometraje)
                                • {{ number_format($vehiculo->kilometraje, 0, ',', '.') }} km
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <a href="{{ route('vehiculo.index') }}"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Volver
            </a>
        </div>

        <!-- Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Total Servicios</p>
                        <p class="text-xl font-bold text-gray-900">{{ $totalServicios }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-500 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Total Gastado</p>
                        <p class="text-xl font-bold text-gray-900">Gs. {{ number_format($totalGastado, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            @if ($vehiculo->mecanico)
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-500 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Mecánico Referidor</p>
                            <p class="text-lg font-bold text-gray-900">{{ $vehiculo->mecanico->name }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($ultimoServicio)
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-500 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-600">Último Servicio</p>
                            <p class="text-lg font-bold text-gray-900">{{ $ultimoServicio->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Observaciones -->
        @if ($vehiculo->observaciones)
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 mt-0.5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-medium text-yellow-800">Observaciones</p>
                        <p class="text-yellow-700 text-sm">{{ $vehiculo->observaciones }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Historial de Servicios -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800">Historial de Servicios</h3>
            </div>

            @if ($vehiculo->ventas->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach ($vehiculo->ventas as $venta)
                        <div class="p-6 hover:bg-gray-50 transition">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="flex-grow">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-sm font-medium text-gray-500">
                                            {{ $venta->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $venta->estado === 'completado'
                                                ? 'bg-green-100 text-green-800'
                                                : ($venta->estado === 'cancelado'
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($venta->estado) }}
                                        </span>
                                    </div>

                                    <!-- Productos/Servicios -->
                                    <div class="space-y-1">
                                        @foreach ($venta->productos->take(3) as $producto)
                                            <p class="text-sm text-gray-700">• {{ $producto->nombre }}</p>
                                        @endforeach
                                        @if ($venta->productos->count() > 3)
                                            <p class="text-xs text-gray-500">+ {{ $venta->productos->count() - 3 }} más</p>
                                        @endif
                                    </div>

                                    @if ($venta->vendedor)
                                        <p class="text-xs text-gray-500 mt-2">Atendido por: {{ $venta->vendedor->name }}
                                        </p>
                                    @endif
                                </div>

                                <div class="text-right">
                                    <p class="text-xl font-bold text-gray-900">Gs.
                                        {{ number_format($venta->total, 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-500 capitalize">{{ $venta->forma_pago }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p>Este vehículo aún no tiene servicios registrados</p>
                </div>
            @endif
        </div>
    </div>
@endsection
