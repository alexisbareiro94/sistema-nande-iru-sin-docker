@extends('layouts.app')

@section('titulo', 'Factura ' . $factura->numero_formateado)

@section('ruta-actual', 'Detalle de Factura')

@section('contenido')
    <header class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('facturas.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-lg md:text-2xl font-bold text-gray-800 font-mono">{{ $factura->numero_formateado }}</h2>
                <p class="text-gray-500 text-sm">Emitida:
                    {{ $factura->emision ? $factura->emision->format('d/m/Y H:i') : $factura->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            {!! $factura->estado_badge !!}
            {!! $factura->tipo_badge !!}
            @if ($factura->estado === 'emitida')
                <button id="btn-anular-factura" data-id="{{ $factura->id }}"
                    class="bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Anular Factura
                </button>
            @endif
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna izquierda - Información --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Info de la Factura --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Datos de Factura
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Timbrado:</span>
                        <span class="font-semibold text-gray-800">{{ $factura->timbrado }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Número:</span>
                        <span class="font-semibold text-gray-800 font-mono">{{ $factura->numero_formateado }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Condición:</span>
                        <span class="text-gray-800 capitalize">{{ $factura->condicion_venta }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tipo:</span>
                        <span class="text-gray-800 capitalize">{{ str_replace('_', ' ', $factura->tipo) }}</span>
                    </div>
                </div>
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
                @if ($factura->venta?->cliente)
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nombre:</span>
                            <span
                                class="font-semibold text-gray-800">{{ $factura->venta->cliente->razon_social ?? $factura->venta->cliente->name }}</span>
                        </div>
                        @if ($factura->venta->cliente->ruc)
                            <div class="flex justify-between">
                                <span class="text-gray-500">RUC/CI:</span>
                                <span class="text-gray-800">{{ $factura->venta->cliente->ruc }}</span>
                            </div>
                        @endif
                        @if ($factura->venta->cliente->telefono)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Teléfono:</span>
                                <span class="text-gray-800">{{ $factura->venta->cliente->telefono }}</span>
                            </div>
                        @endif
                        @if ($factura->venta->cliente->direccion)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Dirección:</span>
                                <span class="text-gray-800">{{ $factura->venta->cliente->direccion }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-gray-400">Sin cliente asignado</p>
                @endif
            </div>

            {{-- Info del Vehículo --}}
            @if ($factura->venta?->vehiculo)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Vehículo
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Patente:</span>
                            <span class="font-semibold text-gray-800">{{ $factura->venta->vehiculo->patente }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Marca/Modelo:</span>
                            <span class="text-gray-800">{{ $factura->venta->vehiculo->marca }}
                                {{ $factura->venta->vehiculo->modelo }}</span>
                        </div>
                        @if ($factura->venta->vehiculo->anio)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Año:</span>
                                <span class="text-gray-800">{{ $factura->venta->vehiculo->anio }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Info del Vendedor --}}
            @if ($factura->venta?->vendedor)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Vendedor
                    </h3>
                    <p class="font-semibold text-gray-800">{{ $factura->venta->vendedor->name }}</p>
                </div>
            @endif
        </div>

        {{-- Columna derecha - Productos y Fotos --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Tabla de productos/servicios --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Productos / Servicios
                </h3>

                @if ($factura->venta?->detalleVentas && $factura->venta->detalleVentas->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto
                                    </th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Cant.
                                    </th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">P. Unit.
                                    </th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($factura->venta->detalleVentas as $detalle)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <span
                                                class="text-sm font-medium text-gray-800">{{ $detalle->producto?->nombre ?? 'Producto eliminado' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-sm text-gray-600">{{ $detalle->cantidad }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="text-sm text-gray-600">Gs.
                                                {{ number_format($detalle->precio_unitario, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="text-sm font-semibold text-gray-800">Gs.
                                                {{ number_format($detalle->subtotal, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totales --}}
                    <div class="mt-4 pt-4 border-t border-gray-200 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal:</span>
                            <span class="text-gray-800">Gs.
                                {{ number_format($factura->venta->subtotal ?? $factura->venta->total, 0, ',', '.') }}</span>
                        </div>
                        @if ($factura->venta->con_descuento && $factura->venta->monto_descuento > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-red-500">Descuento:</span>
                                <span class="text-red-500">- Gs.
                                    {{ number_format($factura->venta->monto_descuento, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span class="text-gray-800">TOTAL:</span>
                            <span class="text-gray-800">Gs.
                                {{ number_format($factura->venta->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-gray-400 text-center py-4">No hay productos registrados</p>
                @endif
            </div>

            {{-- Info de Pago --}}
            @if ($factura->venta?->pagos && $factura->venta->pagos->count() > 0)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Pagos Registrados
                    </h3>
                    <div class="space-y-2">
                        @foreach ($factura->venta->pagos as $pago)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span
                                    class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $pago->metodo ?? ($pago->tipo ?? 'Efectivo')) }}</span>
                                <span class="text-sm font-semibold text-gray-800">Gs.
                                    {{ number_format($pago->monto, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Fotos de la Factura --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Fotos de la Factura
                    </h3>
                    <span class="text-sm text-gray-500">{{ $factura->fotos->count() }} foto(s)</span>
                </div>

                {{-- Formulario para subir foto --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-6 border-2 border-dashed border-gray-300">
                    <form id="form-subir-foto-factura" enctype="multipart/form-data" data-id="{{ $factura->id }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Foto</label>
                                <select name="tipo" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                                    <option value="factura">Factura</option>
                                    <option value="comprobante">Comprobante</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                <input type="text" name="descripcion"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                                    placeholder="Ej: Factura original">
                            </div>
                            <div class="flex justify-between items-center gap-2">
                                <div>
                                    <label class="block w-full text-sm font-medium text-gray-700 mb-2">Seleccionar
                                        Foto</label>
                                    <input type="file" name="foto" id="input-foto-factura" accept="image/*"
                                        required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                                </div>
                                <div class="pt-6">
                                    <button type="button" id="btn-abrir-camara-factura"
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
                        <div id="preview-container-factura" class="hidden mt-4">
                            <div class="flex items-start gap-4 p-3 bg-white rounded-xl border border-gray-200">
                                <div class="relative">
                                    <img id="preview-imagen-factura" src="" alt="Preview"
                                        class="w-32 h-32 object-cover rounded-lg shadow-sm">
                                    <button type="button" id="btn-quitar-preview-factura"
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
                                    <p id="preview-nombre-factura" class="text-xs text-gray-500 mt-1"></p>
                                    <p id="preview-tamano-factura" class="text-xs text-gray-400"></p>
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
                <div id="galeria-fotos-factura" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @forelse ($factura->fotos as $foto)
                        <div class="foto-item relative group rounded-xl overflow-hidden shadow-md"
                            data-foto-id="{{ $foto->id }}">
                            <img src="{{ asset('facturas/' . $foto->ruta_foto) }}" alt="{{ $foto->descripcion }}"
                                class="w-full h-48 object-cover cursor-pointer" onclick="window.open(this.src, '_blank')">
                            <div
                                class="absolute inset-0 bg-black/50 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            {!! $foto->tipo_badge !!}
                                            @if ($foto->descripcion)
                                                <p class="text-white text-sm mt-1">{{ $foto->descripcion }}</p>
                                            @endif
                                        </div>
                                        <button
                                            class="btn-eliminar-foto-factura text-red-400 hover:text-red-300 transition-colors"
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
                            <p class="text-gray-400 text-sm">Sube fotos de la factura física para documentarla</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cámara -->
    <div id="modal-camara-factura" class="fixed inset-0 z-50 hidden items-center justify-center bg-black">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="bg-gray-800 p-4 flex justify-between items-center text-white shrink-0">
                <h3 class="font-bold text-lg">Tomar Foto</h3>
                <button type="button" id="btn-cerrar-camara-factura"
                    class="text-gray-300 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="relative bg-black flex-1 flex items-center justify-center overflow-hidden">
                <video id="video-camara-factura" autoplay playsinline
                    class="max-w-full max-h-full object-contain"></video>
                <canvas id="canvas-camara-factura" class="hidden"></canvas>
            </div>

            <div class="p-4 bg-gray-50 flex justify-center shrink-0">
                <button type="button" id="btn-capturar-foto-factura"
                    class="w-16 h-16 rounded-full bg-white border-4 border-gray-300 flex items-center justify-center shadow-lg hover:bg-gray-100 active:scale-95 transition-all">
                    <div class="w-12 h-12 rounded-full bg-red-600"></div>
                </button>
            </div>
        </div>
    </div>
@endsection
