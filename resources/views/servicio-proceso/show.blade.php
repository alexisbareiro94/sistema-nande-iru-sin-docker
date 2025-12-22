@extends('layouts.app')

@section('titulo', 'Detalle Servicio - ' . $servicio->codigo)

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
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Vehículo
                </h3>
                @if ($servicio->vehiculo)
                    <div class="space-y-3">
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
                @else
                    <div class="flex gap-2">
                        <select name="vehiculo_id" id="vehiculo_id" data-servicio-id="{{ $servicio->id }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                            <option value="">Seleccionar vehículo</option>
                            @foreach ($vehiculos as $vehiculo)
                                {{-- @if ($vehiculo->id != $vehiculo->servicioProceso?->vehiculo_id && $vehiculo->servicioProceso?->estado == 'cobrado') --}}
                                <option value="{{ $vehiculo->id }}">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                    {{ $vehiculo->anio }} | {{ $vehiculo->patente }}</option>
                                {{-- @endif --}}
                            @endforeach
                        </select>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
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
                        <select name="cliente_id" id="cliente_id" data-servicio-id="{{ $servicio->id }}"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                            <option value="">Seleccionar cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->razon_social ?? $cliente->name }}
                                </option>
                            @endforeach
                        </select>
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
                        <select id="select-mecanico-servicio" data-id="{{ $servicio->id }}"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                            <option value="">Sin asignar</option>
                            @foreach ($mecanicos as $m)
                                <option value="{{ $m->id }}">
                                    {{ $m->name }}</option>
                            @endforeach
                        </select>
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

                {{-- Galería de fotos --}}
                <div id="galeria-fotos" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-12 md:mb-1">
                    @forelse ($servicio->fotos as $foto)
                        <div id="ver-foto"
                            class="foto-item cursor-pointer relative group rounded-xl overflow-hidden shadow-md"
                            data-foto-id="{{ $foto->id }}" data-ruta="{{ asset("servicios/$foto->ruta_foto") }}">
                            <img src="{{ asset('servicios/' . $foto->ruta_foto) }}" alt="{{ $foto->descripcion }}"
                                class=" w-full h-48 object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent md:opacity-0 group-hover:opacity-100 transition-opacity"
                                data-foto-id="{{ $foto->id }}"
                                data-ruta="{{ asset("servicios/$foto->ruta_foto") }}">
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            {!! $foto->tipo_badge !!}
                                            @if ($foto->descripcion)
                                                <p class="text-white text-sm mt-1">{{ $foto->descripcion }}</p>
                                            @endif
                                        </div>
                                        <button class="btn-eliminar-foto text-red-400 hover:text-red-300 transition-colors"
                                            data-foto-id="{{ $foto->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-500">No hay fotos aún</p>
                            <p class="text-gray-400 text-sm">Sube fotos del ingreso del vehículo para documentar su estado
                            </p>
                        </div>
                    @endforelse
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
