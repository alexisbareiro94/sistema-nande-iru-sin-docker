{{-- Modal de selección de vehículo y mecánico --}}
<div id="modal-seleccion-servicio"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden animate-fade-in">
        {{-- Header --}}
        <div class="bg-gray-100 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700">Nuevo Servicio en Proceso</h3>
                <button id="btn-cerrar-modal-seleccion" class="text-gray-700 hover:text-gray-900 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <form id="form-nuevo-servicio" class="p-6 space-y-5">
            {{-- Búsqueda de vehículo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Vehículo por Patente</label>
                <div class="flex gap-2">
                    <input type="text" id="input-buscar-patente"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all uppercase"
                        placeholder="Ej: ABC 123">
                    <button type="button" id="btn-buscar-vehiculo"
                        class="px-4 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Resultado de búsqueda --}}
            <div id="resultado-vehiculo" class="hidden">
                <div id="vehiculo-encontrado" class="hidden bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-green-800" id="vehiculo-info"></p>
                            <p class="text-sm text-green-600" id="cliente-info"></p>
                        </div>
                    </div>
                    <input type="hidden" name="vehiculo_id" id="vehiculo_id">
                </div>

                <div id="vehiculo-no-encontrado" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-yellow-800">Vehículo no encontrado</p>
                            <p class="text-sm text-yellow-600">Puedes continuar sin vehículo o registrarlo después</p>
                        </div>
                    </div>
                    <a href="{{ route('vehiculo.index') }}" target="_blank"
                        class="mt-3 inline-block text-sm text-yellow-700 hover:text-yellow-800 underline">
                        Registrar nuevo vehículo →
                    </a>
                </div>
            </div>

            {{-- Selección de mecánico --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mecánico Asignado</label>
                <select name="mecanico_id" id="select-mecanico"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                    <option value="">Seleccionar mecánico (opcional)</option>
                    @foreach ($mecanicos as $mecanico)
                        <option value="{{ $mecanico->id }}">{{ $mecanico->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Descripción inicial --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción del Servicio</label>
                <textarea name="descripcion" id="input-descripcion" rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all resize-none"
                    placeholder="Describe brevemente el servicio a realizar..."></textarea>
            </div>

            {{-- Botones --}}
            <div class="flex gap-3 pt-2">
                <button type="button" id="btn-cancelar-modal"
                    class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium flex items-center justify-center gap-2">
                    <span>Continuar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.2s ease-out;
    }
</style>
