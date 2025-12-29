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
        <div class="flex items-center gap-3">
            {{-- Indicador de timbrado activo --}}
            <div id="indicador-timbrado"
                class="hidden items-center gap-2 px-3 py-2 bg-purple-50 border border-purple-200 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="text-sm text-purple-700">
                    Timbrado: <strong id="timbrado-configurado">---</strong>
                </span>
                <button id="btn-limpiar-timbrado" class="ml-2 text-purple-600 hover:text-purple-800"
                    title="Eliminar configuración">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            {{-- Indicador de configuración activa --}}
            <div id="indicador-config"
                class="hidden items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm text-amber-700">
                    Próximo Nº: <strong id="numero-configurado">---</strong>
                </span>
                <button id="btn-limpiar-config" class="ml-2 text-amber-600 hover:text-amber-800"
                    title="Eliminar configuración">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            {{-- Botón configurar timbrado --}}
            <button id="btn-abrir-timbrado"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 cursor-pointer text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="hidden md:inline">Timbrado</span>
            </button>
            {{-- Botón configurar número --}}
            <button id="btn-abrir-config"
                class="flex items-center gap-2 px-4 py-2 bg-gray-700 cursor-pointer text-white rounded-lg hover:bg-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="hidden md:inline">Nº Factura</span>
            </button>
        </div>
    </header>

    {{-- Modal configurar timbrado --}}
    <div id="modal-config-timbrado" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Configurar Timbrado</h3>
                <p class="text-sm text-gray-500 mt-1">El timbrado se usará para las próximas facturas</p>
            </div>
            <div class="p-6">
                <label for="input-timbrado" class="block text-sm font-medium text-gray-700 mb-2">
                    Número de Timbrado
                </label>
                <input type="number" id="input-timbrado" min="1"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-lg"
                    placeholder="Ej: 18450157">
                <p class="text-xs text-gray-400 mt-2">
                    El timbrado se mantendrá hasta que lo elimines manualmente.
                </p>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end gap-3">
                <button id="btn-cerrar-timbrado"
                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button id="btn-guardar-timbrado"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Guardar
                </button>
            </div>
        </div>
    </div>

    {{-- Modal configurar número --}}
    <div id="modal-config-numero" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Configurar Número de Factura</h3>
                <p class="text-sm text-gray-500 mt-1">El próximo número de factura será el configurado aquí</p>
            </div>
            <div class="p-6">
                <label for="input-numero-factura" class="block text-sm font-medium text-gray-700 mb-2">
                    Número de Factura Inicial
                </label>
                <input type="number" id="input-numero-factura" min="1"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg"
                    placeholder="Ej: 100">
                <p class="text-xs text-gray-400 mt-2">
                    Este número se usará para la próxima factura y luego se eliminará automáticamente.
                </p>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end gap-3">
                <button id="btn-cerrar-config"
                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button id="btn-guardar-config"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Guardar
                </button>
            </div>
        </div>
    </div>

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
