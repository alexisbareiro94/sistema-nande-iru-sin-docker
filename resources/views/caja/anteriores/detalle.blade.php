@extends('layouts.app')
@section('titulo', 'Detalle de Caja #' . $caja->id)
@section('ruta-anterior', 'Cajas Anteriores')
@section('url', route('caja.anteriores'))
@section('ruta-actual', 'Detalle de Caja #' . $caja->id)

@section('contenido')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col hidden md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detalle de Caja #{{ $caja->id }}</h2>
                <p class="text-gray-600 text-sm mt-1">
                    {{ $caja->user->name }} -
                    {{ \Carbon\Carbon::parse($caja->fecha_apertura)->format('d/m/Y H:i') }} a
                    {{ $caja->fecha_cierre ? \Carbon\Carbon::parse($caja->fecha_cierre)->format('d/m/Y H:i') : 'Abierta' }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('caja.anteriores') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>

        <!-- Resumen Financiero -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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
                        <p class="text-sm text-gray-600">Esperado</p>
                        <p class="text-xl font-bold text-gray-900">Gs.
                            {{ number_format($caja->saldo_esperado, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Encontrado</p>
                        <p class="text-xl font-bold text-gray-900">Gs. {{ number_format($caja->monto_cierre, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            @php
                $diferencia = $caja->monto_cierre - $caja->saldo_esperado;
                $esPositivo = $diferencia >= 0;
            @endphp
            <div
                class="bg-gradient-to-br {{ $esPositivo ? 'from-green-50 to-green-100 border-green-200' : 'from-red-50 to-red-100 border-red-200' }} border rounded-xl p-4">
                <div class="flex items-center">
                    <div class="p-2 {{ $esPositivo ? 'bg-green-500' : 'bg-red-500' }} rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Diferencia</p>
                        <p class="text-xl font-bold {{ $esPositivo ? 'text-green-600' : 'text-red-600' }}">
                            Gs. {{ $esPositivo ? '+' : '' }}{{ number_format($diferencia, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-500 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600">Transacciones</p>
                        <p class="text-xl font-bold text-gray-900">{{ $transacciones }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas y Métodos de Pago -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Métodos de Pago -->
            <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Métodos de Pago</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600 font-medium">Efectivo</span>
                            <span class="font-semibold text-green-600">Gs. {{ number_format($efectivo, 0, ',', '.') }}
                                ({{ $efecPorcentaje }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full transition-all duration-300"
                                style="width: {{ $efecPorcentaje }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600 font-medium">Transferencia</span>
                            <span class="font-semibold text-blue-600">Gs. {{ number_format($transferencia, 0, ',', '.') }}
                                ({{ $transfPorcentaje }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-500 h-3 rounded-full transition-all duration-300"
                                style="width: {{ $transfPorcentaje }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Estadísticas</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Ticket Promedio</span>
                        <span class="font-bold text-gray-900">Gs. {{ number_format($promedio, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Mayor Venta</span>
                        <span class="font-bold text-gray-900">Gs. {{ number_format($mayorVenta, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Clientes Únicos</span>
                        <span class="font-bold text-gray-900">{{ $clientes }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 5 Productos Más Vendidos -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Top 5 Productos Más Vendidos
            </h3>
            @if ($ventas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-600 font-medium">#</th>
                                <th class="px-4 py-3 text-left text-gray-600 font-medium">Producto</th>
                                <th class="px-4 py-3 text-center text-gray-600 font-medium">Cantidad</th>
                                <th class="px-4 py-3 text-right text-gray-600 font-medium">Importe</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($ventas as $index => $venta)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $venta['producto'] }}</td>
                                    <td class="px-4 py-3 text-center">{{ $venta['cantidad'] }}</td>
                                    <td class="px-4 py-3 text-right font-semibold text-green-600">Gs.
                                        {{ number_format($venta['total'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay productos vendidos en esta caja</p>
            @endif
        </div>

        <!-- Top 5 Egresos -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                </svg>
                Top 5 Egresos
            </h3>
            @if ($egresos->count() > 0)
                <div class="space-y-3">
                    @foreach ($egresos as $egreso)
                        <div
                            class="flex justify-between items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition">
                            <div>
                                <p class="font-medium text-gray-900">{{ $egreso->concepto }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($egreso->created_at)->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="font-bold text-red-600 text-lg">Gs.
                                {{ number_format($egreso->monto, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="pt-4 mt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700 text-lg">Total Egresos:</span>
                        <span class="font-bold text-red-600 text-xl">Gs.
                            {{ number_format($totalEgreso, 0, ',', '.') }}</span>
                    </div>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay egresos registrados en esta caja</p>
            @endif
        </div>

        <!-- Resumen de Ingresos -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Ingresos Registrados
            </h3>
            @if ($ingresos->count() > 0)
                <div class="space-y-3">
                    @foreach ($ingresos as $ingreso)
                        <div
                            class="flex justify-between items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                            <div>
                                <p class="font-medium text-gray-900">{{ $ingreso->concepto }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($ingreso->created_at)->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="font-bold text-green-600 text-lg">Gs.
                                {{ number_format($ingreso->monto, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="pt-4 mt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700 text-lg">Total Ingresos:</span>
                        <span class="font-bold text-green-600 text-xl">Gs.
                            {{ number_format($totalIngreso, 0, ',', '.') }}</span>
                    </div>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No hay ingresos adicionales registrados en esta caja</p>
            @endif
        </div>

        <!-- Observaciones -->
        @if ($caja->observaciones)
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    Observaciones
                </h3>
                <p class="text-gray-700">{{ $caja->observaciones }}</p>
            </div>
        @endif
    </div>
@endsection
