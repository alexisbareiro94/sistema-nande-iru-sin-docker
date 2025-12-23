@extends('layouts.app')

@section('titulo', 'Facturas')

@section('ruta-actual', 'Facturas')

@section('contenido')
    <header class="flex justify-between md:flex-row md:items-center mb-6 gap-4">
        <div class="flex items-center gap-4">
            <div>
                <h2 class="text-lg md:text-2xl px-2 md:px-0 font-bold text-gray-800">Facturas</h2>
                <p class="text-gray-500 text-sm">Gestión de facturas emitidas</p>
            </div>
        </div>
    </header>

    {{-- Estadísticas rápidas --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Emitidas</p>
            <p class="text-2xl font-bold text-gray-800">{{ $facturas->where('estado', 'emitida')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
            <p class="text-gray-500 text-sm">Anuladas</p>
            <p class="text-2xl font-bold text-gray-800">{{ $facturas->where('estado', 'anulada')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">Total Facturas</p>
            <p class="text-2xl font-bold text-gray-800">{{ $facturas->count() }}</p>
        </div>
    </div>

    {{-- Lista de facturas --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Listado de Facturas</h3>
        </div>

        @if ($facturas->isEmpty())
            <div class="p-12 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500">No hay facturas registradas</p>
                <p class="text-gray-400 text-sm">Las facturas aparecerán aquí cuando se realicen ventas</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nº Factura</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Timbrado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($facturas as $factura)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm font-medium text-gray-900 font-mono">{{ $factura->numero_formateado }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600">{{ $factura->timbrado }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600">
                                        {{ $factura->venta?->cliente?->razon_social ?? ($factura->venta?->cliente?->name ?? 'Sin cliente') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">
                                        Gs. {{ number_format($factura->venta?->total ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-500">
                                        {{ $factura->emision ? $factura->emision->format('d/m/Y H:i') : $factura->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $factura->estado_badge !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('facturas.show', $factura->id) }}"
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
@endsection
