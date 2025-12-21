{{-- public/js/procesar-caja.js --}}
<div id="modal-confirmar-venta"
    class="hidden fixed inset-0 bg-black/30 flex items-center justify-center z-40 transition-opacity duration-300">
    <div class="bg-white md:rounded-2xl w-full max-w-2xl overflow-hidden flex flex-col h-[92vh] shadow-xl">
        <!-- header -->
        <div class="bg-gray-800 p-4 flex justify-between items-center">
            <div class="flex space-x-20 items-center text-center object-center">
                <h2 class="text-white md:text-2xl font-bold flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Confirmar Venta
                </h2>

                <span class="hidden md:flex gap-2 font-medium text-gray-300 items-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    {{ auth()->user()->name }}
                </span>

            </div>
            <!-- cerrar modal x -->
            <button id="cerrar-m-confirmar-ventas"
                class="text-white cursor-pointer hover:bg-gray-700 rounded-full p-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- /header -->

        <div class="px-4 md:px-6 overflow-y-auto flex-1 py-4 space-y-4">
            <!-- datos del cliente -->
            <div id="datos-cliente" class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">Datos del Cliente</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-1">Razón Social</p>
                        <p id="razon-venta" class="font-medium text-gray-800">-</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-1">RUC o CI</p>
                        <p id="ruc-venta" class="font-medium text-gray-800">-</p>
                    </div>
                </div>
            </div>
            <!-- /datos del cliente -->

            <!-- datos del vehiculo y mecanico -->
            <div id="datos-vehiculo" class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">Vehículo y Mecánico</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Patente del vehículo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Patente del Vehículo</label>
                        <div class="flex gap-2">
                            <input type="text" id="input-patente"
                                class="flex-grow px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 focus:border-gray-400 uppercase text-sm"
                                placeholder="ABC123" maxlength="10">
                        </div>
                        <input type="hidden" id="vehiculo-id-venta" value="">
                    </div>

                    <!-- Info del vehículo encontrado -->
                    <div id="info-vehiculo-encontrado" class="hidden">
                        <label class="block text-sm font-medium text-gray-600 mb-2">Vehículo</label>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 flex items-center gap-2">
                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span id="vehiculo-info-texto" class="text-sm text-green-800 font-medium"></span>
                        </div>
                    </div>

                    <!-- Mecánico referidor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-2">Mecánico Referidor
                            <span class="text-gray-400 font-normal">(opcional)</span></label>
                        <div class="flex gap-2">
                            <select id="select-mecanico-venta"
                                class="flex-grow px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm">
                                <option value="">Sin mecánico</option>
                            </select>
                            <button type="button" id="btn-nuevo-mecanico-modal"
                                class="px-3 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition"
                                title="Agregar nuevo mecánico">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Botón nuevo vehículo si no existe -->
                    <div id="cont-btn-nuevo-vehiculo" class="hidden flex items-end">
                        <button type="button" id="btn-nuevo-vehiculo-modal"
                            class="w-full px-3 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Registrar Nuevo Vehículo
                        </button>
                    </div>
                </div>
            </div>
            <!-- /datos del vehiculo y mecanico -->

            <!-- metodo de pago -->
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-5 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">Método de Pago</h3>
                </div>

                <p id="no-radio"
                    class="hidden text-center mb-4 text-red-600 font-medium text-sm py-2 bg-red-50 border border-red-200 rounded-lg">
                    Debes seleccionar un método de pago
                </p>

                <div id="ul-pagos" class="grid grid-cols-3 gap-3 mb-4">
                    <label for="efectivo"
                        class="relative flex flex-col items-center p-4 bg-gray-50 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-100 hover:border-gray-300 transition-all has-[:checked]:bg-green-50 has-[:checked]:border-green-500">
                        <input id="efectivo" type="radio" value="" name="list-radio" class="sr-only">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-7 text-gray-600 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Efectivo</span>
                    </label>

                    <label for="transf"
                        class="relative flex flex-col items-center p-4 bg-gray-50 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-100 hover:border-gray-300 transition-all has-[:checked]:bg-blue-50 has-[:checked]:border-blue-500">
                        <input id="transf" type="radio" value="" name="list-radio" class="sr-only">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-7 text-gray-600 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Transferencia</span>
                    </label>

                    <label for="mixto"
                        class="relative flex flex-col items-center p-4 bg-gray-50 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-100 hover:border-gray-300 transition-all has-[:checked]:bg-purple-50 has-[:checked]:border-purple-500">
                        <input id="mixto" type="radio" value="" name="list-radio" class="sr-only">
                        <div class="flex gap-1 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                            <span class="text-gray-400">/</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Mixto</span>
                    </label>
                </div>

                <!-- monto recibido -->
                <form id="form-monto-recibido" action="">
                    <div id="monto-recibido" class="bg-gray-50 rounded-xl p-4">
                        <div class="flex flex-col md:flex-row md:items-center gap-3">
                            <label for="i-monto-recibido" class="text-sm text-gray-700 font-medium whitespace-nowrap">
                                Monto Recibido:
                            </label>
                            <input
                                class="flex-1 border border-gray-300 px-4 py-2.5 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm"
                                type="number" name="monto-recibido" id="i-monto-recibido" placeholder="0">
                        </div>
                        <p id="vuelto" class="mt-2 text-center font-medium text-gray-700"></p>
                    </div>
                </form>
                <!-- /monto recibido -->
            </div>
            <!-- /metodo de pago -->

            <!-- resumen del carrito -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="flex items-center gap-2 p-4 border-b border-gray-200">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-5 text-orange-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">Productos y Servicios</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-600 uppercase bg-gray-100">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold">Producto</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-center">Cant.</th>
                                <th scope="col" class="px-4 py-3 font-semibold text-right">Precio</th>
                            </tr>
                        </thead>
                        <tbody id="body-tabla-venta" class="divide-y divide-gray-100">
                            <!-- -->
                        </tbody>
                        <tfoot id="footer-tabla-venta" class="bg-gray-50 font-semibold">
                            <!-- -->
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- /resumen del carrito -->
        </div>

        <!-- botones -->
        <div class="border-t border-gray-200 p-4 flex justify-end gap-3 bg-gray-50">
            <button id="cancelar-venta"
                class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-colors">
                Cancelar
            </button>
            <button id="confirmar-venta"
                class="cursor-pointer px-6 py-2.5 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                Confirmar
            </button>
        </div>
        <!-- /botones -->

    </div>
</div>

<script>
    document.getElementById('cerrar-m-confirmar-ventas').addEventListener('click', () => {
        document.getElementById('modal-confirmar-venta').classList.add('hidden')
    })
</script>

<!-- Modal Nuevo Vehículo desde Venta -->
<div id="modal-nuevo-vehiculo-venta"
    class="fixed inset-0 hidden z-50 items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 transform transition-all">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-800 rounded-t-xl">
            <h3 class="text-lg font-semibold text-white">Registrar Nuevo Vehículo</h3>
            <button type="button" id="cerrar-modal-nuevo-vehiculo" class="text-white hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="form-nuevo-vehiculo-venta" class="p-6">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Patente *</label>
                    <input type="text" name="patente" id="nuevo-vehiculo-patente" required maxlength="10"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 uppercase text-sm"
                        placeholder="ABC123">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marca *</label>
                    <input type="text" name="marca" id="nuevo-vehiculo-marca" required maxlength="50"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm"
                        placeholder="Toyota">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modelo *</label>
                    <input type="text" name="modelo" id="nuevo-vehiculo-modelo" required maxlength="100"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm"
                        placeholder="Corolla">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                    <input type="number" name="anio" id="nuevo-vehiculo-anio" min="1900"
                        max="{{ date('Y') + 1 }}"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm"
                        placeholder="{{ date('Y') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="text" name="color" id="nuevo-vehiculo-color" maxlength="30"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm"
                        placeholder="Blanco">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kilometraje</label>
                    <input type="number" name="kilometraje" id="nuevo-vehiculo-km" min="0"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm"
                        placeholder="50000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mecánico</label>
                    <select name="mecanico_id" id="nuevo-vehiculo-mecanico"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm">
                        <option value="">Sin mecánico</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" id="cancelar-nuevo-vehiculo"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition text-sm font-medium">
                    Guardar y Usar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nuevo Mecánico desde Venta -->
<div id="modal-nuevo-mecanico-venta"
    class="fixed inset-0 hidden z-50 items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-800 rounded-t-xl">
            <h3 class="text-lg font-semibold text-white">Registrar Nuevo Mecánico</h3>
            <button type="button" id="cerrar-modal-nuevo-mecanico" class="text-white hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="form-nuevo-mecanico-venta" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="name" id="nuevo-mecanico-nombre" required maxlength="100"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm"
                        placeholder="Nombre del mecánico">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RUC o CI</label>
                    <input type="text" name="ruc_ci" id="nuevo-mecanico-ruc" maxlength="20"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm"
                        placeholder="Ej: 1234567">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="number" name="telefono" id="nuevo-mecanico-telefono"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-400 text-sm"
                        placeholder="Ej: 0981123456">
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" id="cancelar-nuevo-mecanico"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2.5 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition text-sm font-medium">
                    Guardar Mecánico
                </button>
            </div>
        </form>
    </div>
</div>
