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
            {{-- Botón asociar factura --}}
            <button id="btn-abrir-asociar"
                class="flex items-center gap-2 px-4 py-2 bg-emerald-600 cursor-pointer text-white rounded-lg hover:bg-emerald-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <span class="hidden md:inline">Asociar Factura</span>
            </button>
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

    {{-- Modal asociar factura --}}
    <div id="modal-asociar-factura" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Asociar Factura a Venta</h3>
                <p class="text-sm text-gray-500 mt-1">Busca una venta por código y selecciona el cliente</p>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto">
                {{-- Paso 1: Buscar venta por código --}}
                <div>
                    <label for="input-codigo-venta" class="block text-sm font-medium text-gray-700 mb-2">
                        Código de Venta
                    </label>
                    <div class="flex gap-2">
                        <input type="text" id="input-codigo-venta"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            placeholder="Ej: V-0001234">
                        <button id="btn-buscar-venta" type="button"
                            class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Estado de carga buscando venta --}}
                <div id="loader-venta" class="hidden text-center py-4">
                    <svg class="animate-spin h-8 w-8 mx-auto text-emerald-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <p class="text-gray-500 mt-2">Buscando venta...</p>
                </div>

                {{-- Información de la venta encontrada --}}
                <div id="info-venta-encontrada" class="hidden bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium text-emerald-800">Venta encontrada</span>
                    </div>
                    <div class="text-sm text-gray-700 space-y-1">
                        <p><strong>Código:</strong> <span id="venta-codigo">---</span></p>
                        <p><strong>Total:</strong> Gs. <span id="venta-total">---</span></p>
                        <p><strong>Fecha:</strong> <span id="venta-fecha">---</span></p>
                        <p><strong>Cliente actual:</strong> <span id="venta-cliente-actual">---</span></p>
                    </div>
                    <input type="hidden" id="venta-id-seleccionada">
                </div>

                {{-- Error de venta --}}
                <div id="error-venta" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="error-venta-mensaje" class="text-red-700 text-sm">Venta no encontrada</span>
                    </div>
                </div>

                {{-- Paso 2: Seleccionar cliente (visible después de encontrar la venta) --}}
                <div id="seccion-cliente" class="hidden">
                    <label for="input-buscar-cliente" class="block text-sm font-medium text-gray-700 mb-2">
                        Buscar Cliente
                    </label>
                    <input type="text" id="input-buscar-cliente"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Nombre, RUC o razón social...">

                    {{-- Lista de clientes --}}
                    <div id="lista-clientes"
                        class="mt-2 max-h-48 overflow-y-auto border border-gray-200 rounded-lg hidden">
                    </div>

                    {{-- Cliente seleccionado --}}
                    <div id="cliente-seleccionado" class="hidden mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Cliente seleccionado:</p>
                                <p class="font-medium text-blue-800" id="cliente-nombre-seleccionado">---</p>
                                <p class="text-xs text-gray-500" id="cliente-ruc-seleccionado">---</p>
                            </div>
                            <button id="btn-quitar-cliente" type="button" class="text-red-500 hover:text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <input type="hidden" id="cliente-id-seleccionado">
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end gap-3">
                <button id="btn-cerrar-asociar"
                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button id="btn-guardar-asociar" disabled
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Asociar Factura
                </button>
            </div>
        </div>
    </div>

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
