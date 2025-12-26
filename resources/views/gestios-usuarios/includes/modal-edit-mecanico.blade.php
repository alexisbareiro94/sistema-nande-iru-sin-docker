<div id="modal-edit-mecanico"
    class="hidden fixed inset-0 bg-black/20 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-2xl p-6 space-y-6">

        <!-- Encabezado -->
        <div class="flex justify-between items-center pb-2 border-b">
            <h2 class="text-xl font-bold text-gray-800">Editar Mecánico</h2>
            <button id="cerrar-modal-edit-mecanico"
                class="cursor-pointer text-gray-400 hover:text-gray-600 text-2xl leading-none transition-colors">
                &times;
            </button>
        </div>

        <div class="space-y-3">
            <form class="max-w-sm mx-auto" id="form-edit-mecanico-gcd">
                <input type="hidden" name="mecanico-id" id="mecanico-id" value="">
                <div class="mb-5">
                    <label for="mecanico-nombre-gcd" class="block mb-2 text-sm font-medium text-gray-900">Nombre
                        *</label>
                    <input type="text" id="mecanico-nombre-gcd"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        placeholder="Nombre del mecánico" required />
                </div>
                <div class="mb-5">
                    <label for="mecanico-ruc-ci-gcd" class="block mb-2 text-sm font-medium text-gray-900">RUC /
                        CI</label>
                    <input type="text" id="mecanico-ruc-ci-gcd"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        placeholder="Ej: 1234567" />
                </div>
                <div class="mb-5">
                    <label for="mecanico-telefono-gcd"
                        class="block mb-2 text-sm font-medium text-gray-900">Teléfono</label>
                    <input type="text" id="mecanico-telefono-gcd"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        placeholder="Ej: 0981123456" />
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-3 pt-2">
                    <button type="button" id="btn-cancelar-edit-mecanico"
                        class="flex-1 px-4 py-3 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="cursor-pointer transition-all active:scale-90 flex-1 px-4 py-3 rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium shadow-md hover:shadow-lg">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar Mecánico -->
<div id="modal-eliminar-mecanico-gcd"
    class="hidden fixed inset-0 bg-black/20 backdrop-blur-xs flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-2xl p-6 space-y-6">
        <div class="flex justify-between items-center pb-2 border-b">
            <h3 id="h3-eliminar-mecanico" class="text-xl font-bold text-gray-800">¿Eliminar mecánico?</h3>
            <button
                class="cerrar-modal-mecanico cursor-pointer text-gray-400 hover:text-gray-600 text-2xl leading-none transition-colors">
                &times;
            </button>
        </div>
        <p class="text-gray-600">Esta acción no se puede deshacer.</p>
        <div class="flex gap-3 pt-2">
            <button type="button"
                class="cerrar-modal-mecanico flex-1 px-4 py-3 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium transition-colors">
                Cancelar
            </button>
            <button type="button" id="btn-eliminar-mecanico" data-id=""
                class="cursor-pointer transition-all active:scale-90 flex-1 px-4 py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white font-medium shadow-md hover:shadow-lg">
                Eliminar
            </button>
        </div>
    </div>
</div>
