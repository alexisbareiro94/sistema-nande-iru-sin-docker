@extends('layouts.app')
@section('titulo', 'Vehículos')
@section('ruta-anterior', 'Inicio')
@section('url', route('home'))
@section('ruta-actual', 'Vehículos')

@section('contenido')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Vehículos Registrados</h2>
                <p class="text-gray-600 text-sm mt-1">Gestiona y visualiza el historial de vehículos atendidos</p>
            </div>
            <button onclick="abrirModalNuevoVehiculoVehiculos()"
                class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition flex items-center cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Vehículo
            </button>
        </div>

        <!-- Buscador -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-grow relative">
                    <span class="absolute left-3 top-2.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </span>
                    <input id="input-buscar" type="text" placeholder="Buscar por patente, marca o modelo..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        value="{{ request('search') }}">
                </div>
                <button onclick="buscarVehiculos()"
                    class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition cursor-pointer">
                    Buscar
                </button>
            </div>
        </div>

        <!-- Botón y Panel de Estadísticas -->
        <div class="mb-6">
            <button onclick="toggleEstadisticasVehiculos()" id="btn-estadisticas"
                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center cursor-pointer shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span id="btn-estadisticas-text">Ver Estadísticas</span>
                <svg xmlns="http://www.w3.org/2000/svg" id="icon-estadisticas-arrow"
                    class="h-4 w-4 ml-2 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Panel de Estadísticas (oculto por defecto) -->
            <div id="panel-estadisticas" class="hidden mt-4 bg-white rounded-xl shadow-sm p-6 transition-all">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Estadísticas de Vehículos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Total de Vehículos -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-500 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-blue-600 font-medium">Total Vehículos</p>
                                <p class="text-2xl font-bold text-blue-800">
                                    {{ $marcasModelos['total'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cantidad de Marcas -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-green-600 font-medium">Marcas</p>
                                <p class="text-2xl font-bold text-green-800">{{ count($marcasModelos['marcas']) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cantidad de Modelos -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-500 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-purple-600 font-medium">Modelos</p>
                                <p class="text-2xl font-bold text-purple-800">{{ count($marcasModelos['modelos']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desglose por Marca y Modelo -->
                <div class="mt-6 border-t pt-6">
                    <h4 class="text-md font-semibold text-gray-700 mb-4">Desglose por Marca y Modelo</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                        @foreach ($marcasModelos['marcas'] as $marca)
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="font-semibold text-gray-800">{{ $marca->marca }}</h5>
                                    <span
                                        class="text-sm font-bold bg-gray-800 text-white px-2 py-1 rounded">{{ $marca->count }}</span>
                                </div>
                                <ul class="space-y-2 text-sm">
                                    @foreach ($marcasModelos['modelos'] as $modelo)
                                        @if ($modelo->marca == $marca->marca)
                                            <li class="flex justify-between items-center text-gray-600">
                                                <span>{{ $modelo->modelo }}</span>
                                                <span
                                                    class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded font-medium">{{ $modelo->count }}</span>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Vehículos -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600 font-medium">Patente</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-medium">Vehículo</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-medium">Año</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-medium">Cliente/Mecánico</th>
                            <th class="px-4 py-3 text-center text-gray-600 font-medium">Servicios</th>
                            <th class="px-4 py-3 text-left text-gray-600 font-medium">Último Servicio</th>
                            <th class="px-4 py-3 text-center text-gray-600 font-medium">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($vehiculos as $vehiculo)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <span class="font-bold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                        {{ $vehiculo->patente }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $vehiculo->marca }}
                                            {{ $vehiculo->modelo }}
                                        </p>
                                        @if ($vehiculo->color)
                                            <p class="text-xs text-gray-500">{{ $vehiculo->color }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $vehiculo->anio ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($vehiculo->mecanico)
                                        <p class="text-sm text-gray-900">{{ $vehiculo->mecanico->name }}</p>
                                        <p class="text-xs text-gray-500">Mecánico</p>
                                    @elseif($vehiculo->cliente)
                                        <p class="text-sm text-gray-900">{{ $vehiculo->cliente->name }}</p>
                                        <p class="text-xs text-gray-500">Cliente</p>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ $vehiculo->servicios_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    @if ($vehiculo->ultimaVenta)
                                        {{ $vehiculo->ultimaVenta->created_at->format('d/m/Y') }}
                                    @else
                                        <span class="text-gray-400">Sin servicios</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('vehiculo.show', $vehiculo->id) }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium" title="Ver Historial">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <button onclick="abrirModalEditarVehiculoVehiculos({{ json_encode($vehiculo) }})"
                                            class="cursor-pointer text-gray-500 hover:text-blue-600 transition"
                                            title="Editar Vehículo">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No hay vehículos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-4 py-3 border-t">
                {{ $vehiculos->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Vehículo -->
    <div id="modal-nuevo-vehiculo"
        class="fixed inset-0 hidden z-50 items-center justify-center bg-black/30 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 transform transition-all">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Registrar Nuevo Vehículo</h3>
                <button type="button" onclick="cerrarModalNuevoVehiculoVehiculos()"
                    class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="form-nuevo-vehiculo" class="p-6">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Patente *</label>
                        <input type="text" name="patente" required maxlength="10"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 uppercase"
                            placeholder="ABC123">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marca *</label>
                        <input type="text" name="marca" required maxlength="50"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Toyota">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Modelo *</label>
                        <input type="text" name="modelo" required maxlength="100"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Corolla">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                        <input type="number" name="anio" min="1900" max="{{ date('Y') + 1 }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="{{ date('Y') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="text" name="color" maxlength="30"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Blanco">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kilometraje</label>
                        <input type="number" name="kilometraje" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="50000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                        <select name="cliente_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mecánico Referidor</label>
                        <select name="mecanico_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            @foreach ($mecanicos as $mecanico)
                                <option value="{{ $mecanico->id }}">{{ $mecanico->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea name="observaciones" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Notas adicionales..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="cerrarModalNuevoVehiculoVehiculos()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Vehículo -->
    <div id="modal-editar-vehiculo"
        class="fixed inset-0 hidden z-50 items-center justify-center bg-black/30 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 transform transition-all">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Editar Vehículo</h3>
                <button type="button" onclick="cerrarModalEditarVehiculoVehiculos()"
                    class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="form-editar-vehiculo" class="p-6">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_vehiculo_id" name="vehiculo_id">

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Patente *</label>
                        <input type="text" id="edit_patente" name="patente" required maxlength="10"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 uppercase"
                            placeholder="ABC123">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marca *</label>
                        <input type="text" id="edit_marca" name="marca" required maxlength="50"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Toyota">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Modelo *</label>
                        <input type="text" id="edit_modelo" name="modelo" required maxlength="100"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Corolla">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                        <input type="number" id="edit_anio" name="anio" min="1900" max="{{ date('Y') + 1 }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="{{ date('Y') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input type="text" id="edit_color" name="color" maxlength="30"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Blanco">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kilometraje</label>
                        <input type="number" id="edit_kilometraje" name="kilometraje" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="50000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                        <select id="edit_cliente_id" name="cliente_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mecánico Referidor</label>
                        <select id="edit_mecanico_id" name="mecanico_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            @foreach ($mecanicos as $mecanico)
                                <option value="{{ $mecanico->id }}">{{ $mecanico->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea id="edit_observaciones" name="observaciones" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Notas adicionales..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="cerrarModalEditarVehiculoVehiculos()"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('js')
    <script>
        function toggleEstadisticasVehiculos() {
            const panel = document.getElementById('panel-estadisticas');
            const btnText = document.getElementById('btn-estadisticas-text');
            const arrow = document.getElementById('icon-estadisticas-arrow');

            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                btnText.textContent = 'Ocultar Estadísticas';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                panel.classList.add('hidden');
                btnText.textContent = 'Ver Estadísticas';
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        function abrirModalNuevoVehiculoVehiculos() {
            document.getElementById('modal-nuevo-vehiculo').classList.remove('hidden');
            document.getElementById('modal-nuevo-vehiculo').classList.add('flex');
        }

        function cerrarModalNuevoVehiculoVehiculos() {
            document.getElementById('modal-nuevo-vehiculo').classList.add('hidden');
            document.getElementById('modal-nuevo-vehiculo').classList.remove('flex');
        }

        function abrirModalEditarVehiculoVehiculos(vehiculo) {
            document.getElementById('edit_vehiculo_id').value = vehiculo.id;
            document.getElementById('edit_patente').value = vehiculo.patente;
            document.getElementById('edit_marca').value = vehiculo.marca;
            document.getElementById('edit_modelo').value = vehiculo.modelo;
            document.getElementById('edit_anio').value = vehiculo.anio || '';
            document.getElementById('edit_color').value = vehiculo.color || '';
            document.getElementById('edit_kilometraje').value = vehiculo.kilometraje || '';

            document.getElementById('edit_cliente_id').value = vehiculo.cliente_id || '';
            document.getElementById('edit_mecanico_id').value = vehiculo.mecanico_id || '';
            document.getElementById('edit_observaciones').value = vehiculo.observaciones || '';

            document.getElementById('modal-editar-vehiculo').classList.remove('hidden');
            document.getElementById('modal-editar-vehiculo').classList.add('flex');
        }

        function cerrarModalEditarVehiculoVehiculos() {
            document.getElementById('modal-editar-vehiculo').classList.add('hidden');
            document.getElementById('modal-editar-vehiculo').classList.remove('flex');
        }

        function buscarVehiculos() {
            const search = document.getElementById('input-buscar').value;
            window.location.href = `{{ route('vehiculo.index') }}?search=${encodeURIComponent(search)}`;
        }

        document.getElementById('input-buscar').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') buscarVehiculos();
        });

        document.getElementById('form-nuevo-vehiculo').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('{{ route('vehiculo.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showToast(result.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(result.error, 'error');
                }
            } catch (error) {
                showToast('Error al guardar el vehículo', 'error');
            }
        });

        document.getElementById('form-editar-vehiculo').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('edit_vehiculo_id').value;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`/vehiculos/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Vehículo actualizado correctamente', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(result.error || 'Error al actualizar', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Error al procesar la solicitud', 'error');
            }
        });
    </script>
@endsection
