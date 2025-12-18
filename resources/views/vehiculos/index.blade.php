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
            <button onclick="abrirModalNuevoVehiculo()"
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
                                        <p class="font-medium text-gray-900">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}
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
                                    <a href="{{ route('vehiculo.show', $vehiculo->id) }}"
                                        class="text-blue-600 hover:text-blue-800 font-medium">
                                        Ver Historial
                                    </a>
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
                <button onclick="cerrarModalNuevoVehiculo()" class="text-gray-500 hover:text-gray-700">
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
                    <button type="button" onclick="cerrarModalNuevoVehiculo()"
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

    @section('js')
        <script>
            function abrirModalNuevoVehiculo() {
                document.getElementById('modal-nuevo-vehiculo').classList.remove('hidden');
                document.getElementById('modal-nuevo-vehiculo').classList.add('flex');
            }

            function cerrarModalNuevoVehiculo() {
                document.getElementById('modal-nuevo-vehiculo').classList.add('hidden');
                document.getElementById('modal-nuevo-vehiculo').classList.remove('flex');
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
        </script>
    @endsection
@endsection
