@extends('layouts.app')
@section('titulo', 'Dashboard')
@section('ruta-actual', 'Dashboard')

@section('contenido')
    <div class="p-4 md:p-6">
        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Dashboard de Estadísticas</h2>
                <p class="text-gray-600 text-sm">Resumen general de movimientos, ventas y rendimiento</p>
            </div>

            <!-- Filtros de período -->
            <div class="flex flex-col items-end gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Período:</span>
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button data-periodo="1"
                            class="btn-periodo px-3 py-1.5 text-sm rounded-md transition-all">Hoy</button>
                        <button data-periodo="7"
                            class="btn-periodo px-3 py-1.5 text-sm rounded-md transition-all bg-gray-800 text-white">7
                            días</button>
                        <button data-periodo="30" class="btn-periodo px-3 py-1.5 text-sm rounded-md transition-all">30
                            días</button>
                        <button data-periodo="90" class="btn-periodo px-3 py-1.5 text-sm rounded-md transition-all">90
                            días</button>
                        <button id="btn-personalizado" class="btn-periodo px-3 py-1.5 text-sm rounded-md transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Personalizado
                        </button>
                    </div>
                </div>

                <!-- Panel de fechas personalizadas (oculto por defecto) -->
                <div id="panel-fechas-custom"
                    class="hidden flex items-center gap-2 bg-white px-3 py-2 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Desde:</label>
                        <input type="date" id="fecha-inicio-input"
                            class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-gray-400 focus:border-gray-400">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Hasta:</label>
                        <input type="date" id="fecha-fin-input"
                            class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:ring-2 focus:ring-gray-400 focus:border-gray-400">
                    </div>
                    <button id="btn-aplicar-fechas"
                        class="bg-gray-800 text-white px-3 py-1.5 text-sm rounded-md hover:bg-gray-700 transition-all flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Aplicar
                    </button>
                </div>

                <!-- Rango de fechas -->
                <div id="rango-fechas"
                    class="flex items-center gap-2 text-sm text-gray-600 bg-gray-100 px-3 py-1.5 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span id="fecha-desde">{{ $datos['fecha_inicio'] }}</span>
                    <span>—</span>
                    <span id="fecha-hasta">{{ $datos['fecha_fin'] }}</span>
                </div>
            </div>
        </header>
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Panel de Control</h1>
            @if (auth()->check() && !auth()->user()->temp_used && auth()->user()->role == 'admin')
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Cuenta temporal</p>
                    <p>Esta cuenta es temporal y vencerá el
                        <strong>{{ format_time(auth()->user()->expires_at) }}</strong>.
                        Por favor, configurá tu cuenta permanente antes de esa fecha.
                    </p>
                    <a href="{{ route('auth.config.view') }}"
                        class="text-blue-600 underline font-medium hover:text-blue-800">
                        Configurar ahora
                    </a>
                </div>
            @endif
        </div>
        <!-- Cards de resumen -->
        @include('dashboard.includes.resumen-movimientos')

        <!-- Grid principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Gráfico de formas de pago -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                @include('dashboard.includes.formas-pago')
            </div>

            <!-- Top productos -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
                @include('dashboard.includes.top-productos')
            </div>
        </div>

        <!-- Estadísticas de cajeros -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            @include('dashboard.includes.cajeros-stats')
        </div>
    </div>

@section('js')
    {{-- Chart.js se carga desde Vite en resources/js/dashboard.js --}}
@endsection
@endsection
