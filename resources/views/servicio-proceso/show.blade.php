@extends('layouts.app')

@section('titulo', 'Detalle Servicio - ' . $servicio->codigo)

@section('ruta-anterior', 'Servicios')

@section('url', '/servicio-proceso')

@section('ruta-actual', 'Detalle del Servicio')

@section('contenido')
    <header class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('servicio.proceso.index') }}"
                class="p-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-lg md:text-2xl font-bold text-gray-800">{{ $servicio->codigo }}</h2>
                <p class="text-gray-500 text-sm">Creado: {{ $servicio->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            {!! $servicio->estado_badge !!}
            @if ($servicio->estado != 'cobrado')
                <select id="select-estado-servicio" data-id="{{ $servicio->id }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                    <option value="pendiente" {{ $servicio->estado == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="en_proceso" {{ $servicio->estado == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="completado" {{ $servicio->estado == 'completado' ? 'selected' : '' }}>Completado</option>
                    <option value="cancelado" {{ $servicio->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    <option value="cobrado" {{ $servicio->estado == 'cobrado' ? 'selected' : '' }}>Pagado</option>
                </select>
            @endif
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna izquierda - Información --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Info del Vehículo --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Vehículo
                    </h3>
                    @if ($servicio->vehiculo && $servicio->estado != 'cobrado')
                        {{-- Menú desplegable de opciones --}}
                        <div class="relative" id="vehiculo-menu-container">
                            <button type="button" id="btn-toggle-vehiculo-menu"
                                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            {{-- Dropdown menu --}}
                            <div id="vehiculo-dropdown-menu"
                                class="hidden absolute right-0 top-full mt-1 w-40 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                                <button type="button" id="btn-editar-vehiculo"
                                    class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                    Cambiar
                                </button>
                                <button type="button" id="btn-quitar-vehiculo" data-servicio-id="{{ $servicio->id }}"
                                    class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Quitar
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
                @if ($servicio->vehiculo)
                    {{-- Mostrar información del vehículo --}}
                    <div id="vehiculo-info-display" class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Patente:</span>
                            <span class="font-semibold text-gray-800">{{ $servicio->vehiculo->patente }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Marca:</span>
                            <span class="text-gray-800">{{ $servicio->vehiculo->marca }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Modelo:</span>
                            <span class="text-gray-800">{{ $servicio->vehiculo->modelo }}</span>
                        </div>
                        @if ($servicio->vehiculo->anio)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Año:</span>
                                <span class="text-gray-800">{{ $servicio->vehiculo->anio }}</span>
                            </div>
                        @endif
                        @if ($servicio->vehiculo->color)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Color:</span>
                                <span class="text-gray-800">{{ $servicio->vehiculo->color }}</span>
                            </div>
                        @endif
                    </div>
                    {{-- Formulario para cambiar vehículo (oculto inicialmente) --}}
                    <div id="vehiculo-edit-form" class="hidden space-y-3">
                        <div class="flex gap-2">
                            <div class="flex-1 relative" data-vehiculos='@json($vehiculos)'>
                                <input type="hidden" name="vehiculo_id" id="vehiculo_id_edit"
                                    data-servicio-id="{{ $servicio->id }}" value="{{ $servicio->vehiculo_id }}">
                                <input type="text" id="vehiculo_search_edit"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                    placeholder="Buscar por patente, marca o modelo..."
                                    value="{{ $servicio->vehiculo ? $servicio->vehiculo->marca . ' ' . $servicio->vehiculo->modelo . ' | ' . $servicio->vehiculo->patente : '' }}"
                                    autocomplete="off">
                                <div id="vehiculo_results_edit"
                                    class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                    {{-- Resultados dinámicos --}}
                                </div>
                            </div>
                            <button type="button" id="btn-abrir-modal-vehiculo"
                                class="px-3 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors"
                                title="Agregar nuevo vehículo">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" id="btn-cancelar-editar-vehiculo"
                                class="flex-1 px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                Cancelar
                            </button>
                            <button type="button" id="btn-guardar-vehiculo" data-servicio-id="{{ $servicio->id }}"
                                class="flex-1 px-3 py-2 text-sm bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                Guardar
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex gap-2">
                        <div class="flex-1 relative" data-vehiculos='@json($vehiculos)'>
                            <input type="hidden" name="vehiculo_id" id="vehiculo_id"
                                data-servicio-id="{{ $servicio->id }}" value="">
                            <input type="text" id="vehiculo_search"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                placeholder="Buscar por patente, marca o modelo..." autocomplete="off">
                            <div id="vehiculo_results"
                                class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                {{-- Resultados dinámicos --}}
                            </div>
                        </div>
                        <button type="button" id="btn-abrir-modal-vehiculo"
                            class="px-3 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors"
                            title="Agregar nuevo vehículo">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
            {{-- Info del Cliente --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Cliente
                </h3>
                @if ($servicio->cliente)
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nombre:</span>
                            <span
                                class="font-semibold text-gray-800">{{ $servicio->cliente->razon_social ?? $servicio->cliente->name }}</span>
                        </div>
                        @if ($servicio->cliente->telefono)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Teléfono:</span>
                                <span class="text-gray-800">{{ $servicio->cliente->telefono }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex gap-2">
                        <div class="flex-1 relative" data-clientes='@json($clientes)'>
                            <input type="hidden" name="cliente_id" id="cliente_id"
                                data-servicio-id="{{ $servicio->id }}" value="">
                            <input type="text" id="cliente_search"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                placeholder="Buscar por nombre, razón social o teléfono..." autocomplete="off">
                            <div id="cliente_results"
                                class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                {{-- Resultados dinámicos --}}
                            </div>
                        </div>
                        <button type="button" id="btn-abrir-modal-cliente"
                            class="px-3 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors"
                            title="Agregar nuevo cliente">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Mecánico asignado --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Mecánico
                </h3>
                <div class="flex gap-2">
                    @if ($servicio->mecanico)
                        <span class="font-semibold text-gray-800">{{ $servicio->mecanico->name }}</span>
                    @else
                        <div class="flex-1 relative" data-mecanicos='@json($mecanicos)'>
                            <input type="hidden" name="mecanico_id" id="mecanico_id"
                                data-servicio-id="{{ $servicio->id }}" value="">
                            <input type="text" id="mecanico_search"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                placeholder="Buscar mecánico por nombre..." autocomplete="off">
                            <div id="mecanico_results"
                                class="absolute z-20 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
                                {{-- Resultados dinámicos --}}
                            </div>
                        </div>
                        <button type="button" id="btn-abrir-modal-mecanico"
                            class="px-3 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors"
                            title="Agregar nuevo mecánico">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Observaciones --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Observaciones</h3>
                <textarea id="textarea-observaciones" data-id="{{ $servicio->id }}" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent resize-none"
                    placeholder="Agregar observaciones...">{{ $servicio->observaciones }}</textarea>
                <button id="btn-guardar-observaciones"
                    class="mt-3 w-full bg-gray-800 text-white py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    Guardar Observaciones
                </button>
            </div>
        </div>

        {{-- Columna derecha - Fotos --}}
        <div class="lg:col-span-2 relative">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Fotos del Servicio
                    </h3>
                    <span class="text-sm text-gray-500">{{ $servicio->fotos->count() }} foto(s)</span>
                </div>

                {{-- Formulario para subir foto --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-6 border-2 border-dashed border-gray-300">
                    <form id="form-subir-foto" enctype="multipart/form-data" data-id="{{ $servicio->id }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Foto</label>
                                <select name="tipo" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                                    <option value="ingreso">Ingreso</option>
                                    <option value="proceso" {{ $servicio->estado == 'en_proceso' ? 'selected' : '' }}>
                                        Durante el Proceso</option>
                                    <option value="entrega">Entrega</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                <input type="text" name="descripcion"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                    placeholder="Ej: Rayón en puerta izquierda">
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <div>
                                    <label class="block w-full text-sm font-medium text-gray-700 mb-2">Seleccionar
                                        Foto</label>
                                    <input type="file" name="foto" id="input-foto-servicio" accept="image/*"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                </div>
                                <div class="pt-6">
                                    <button type="button" id="btn-abrir-camara"
                                        class="w-full px-4 py-3 mt-1 bg-gray-800 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Preview de la imagen --}}
                        <div id="preview-container" class="hidden mt-4">
                            <div class="flex items-start gap-4 p-3 bg-white rounded-xl border border-gray-200">
                                <div class="relative">
                                    <img id="preview-imagen" src="" alt="Preview"
                                        class="w-32 h-32 object-cover rounded-lg shadow-sm">
                                    <button type="button" id="btn-cancelar-preview"
                                        class="absolute z-999 -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-700">Vista previa</p>
                                    <p id="preview-nombre" class="text-xs text-gray-500 mt-1"></p>
                                    <p id="preview-tamano" class="text-xs text-gray-400"></p>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="mt-4 w-full bg-gray-800 text-white py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Subir Foto
                        </button>
                    </form>
                </div>

                {{-- Galería de fotos - Renderizado dinámico desde Google Drive --}}
                <div id="galeria-fotos-servicio"
                    class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-12 md:mb-1">
                    {{-- Contenido cargado por JavaScript --}}
                </div>
            </div>

            @if ($servicio->estado != 'cobrado')
                <div class="absolute bottom-0 right-0 items-center justify-center p-3 md:p-0">
                    <button data-servicio="{{ json_encode($servicio) }}" id="btn-procesar-cobro" type="button"
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition active:scale-90 font-semibold cursor-pointer">
                        Procesar Cobro
                    </button>
                </div>
            @endif

            {{-- Servicios Realizados --}}
            @if ($servicio->estado == 'cobrado' && $servicio->venta && $servicio->venta->productos->count() > 0)
                <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Servicios Realizados
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-2 text-sm font-semibold text-gray-600">Servicio</th>
                                    <th class="text-center py-3 px-2 text-sm font-semibold text-gray-600">Cant.</th>
                                    <th class="text-right py-3 px-2 text-sm font-semibold text-gray-600">Precio</th>
                                    <th class="text-right py-3 px-2 text-sm font-semibold text-gray-600">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($servicio->venta->productos as $producto)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="py-3 px-2">
                                            <span class="font-medium text-gray-800">{{ $producto->nombre }}</span>
                                        </td>
                                        <td class="py-3 px-2 text-center text-gray-600">
                                            {{ $producto->pivot->cantidad ?? 1 }}
                                        </td>
                                        <td class="py-3 px-2 text-right text-gray-600">
                                            ₲
                                            {{ number_format($producto->precio_venta, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-2 text-right font-semibold text-gray-800">
                                            ₲
                                            {{ number_format($producto->precio_venta * ($producto->pivot->cantidad ?? 1), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                @if ($servicio->venta->descuento > 0)
                                    <tr class="border-t border-gray-200">
                                        <td colspan="3" class="py-2 px-2 text-right text-gray-500">Descuento:</td>
                                        <td class="py-2 px-2 text-right text-red-500 font-medium">
                                            - ₲ {{ number_format($servicio->venta->descuento, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                                <tr class="border-t-2 border-gray-300">
                                    <td colspan="3" class="py-3 px-2 text-right font-bold text-gray-800 text-lg">TOTAL:
                                    </td>
                                    <td class="py-3 px-2 text-right font-bold text-green-600 text-lg">
                                        ₲ {{ number_format($servicio->venta->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div
                        class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
                        <span>Método de pago: <strong
                                class="text-gray-700">{{ ucfirst($servicio->venta->metodo_pago ?? 'N/A') }}</strong></span>
                        <span>Fecha: <strong
                                class="text-gray-700">{{ $servicio->venta->created_at->format('d/m/Y H:i') }}</strong></span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modales para crear --}}
    @include('servicio-proceso.includes.modales-creacion')
    {{-- modal ventas --}}
    @include('caja.includes.modal-venta')
    {{-- modal add-cliente --}}
    @include('caja.includes.modal-add-clientes')

    @include('caja.includes.cargando')

    @include('caja.venta-completada')

    @include('servicio-proceso.includes.modal-ver-fotos')

    <!-- Modal Cámara -->
    <div id="modal-camara" class="fixed inset-0 z-50 hidden items-center justify-center bg-black">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="bg-gray-800 p-4 flex justify-between items-center text-white shrink-0">
                <h3 class="font-bold text-lg">Tomar Foto</h3>
                <button type="button" id="btn-cerrar-camara" class="text-gray-300 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="relative bg-black flex-1 flex items-center justify-center overflow-hidden">
                <video id="video-camara" autoplay playsinline class="max-w-full max-h-full object-contain"></video>
                <canvas id="canvas-camara" class="hidden"></canvas>
            </div>

            <div class="p-4 bg-gray-50 flex justify-center shrink-0">
                <button type="button" id="btn-tomar-foto"
                    class="w-16 h-16 rounded-full bg-white border-4 border-gray-300 flex items-center justify-center shadow-lg hover:bg-gray-100 active:scale-95 transition-all">
                    <div class="w-12 h-12 rounded-full bg-red-600"></div>
                </button>
            </div>
        </div>
    </div>
@endsection
