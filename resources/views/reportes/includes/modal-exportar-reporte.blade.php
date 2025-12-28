<!-- Modal para exportar reporte global -->
<div id="modal-exportar-reporte"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden animate-fade-in">
        <!-- Header -->
        <div class="bg-white px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 text-white">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700">Exportar Reporte Global</h3>
                </div>
                <button onclick="cerrarModalExportar()"
                    class="cursor-pointer text-gray-700/80 hover:text-gray-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6">
            <p class="text-gray-600 mb-6">
                Selecciona el rango de fechas para generar el reporte completo en formato Excel.
            </p>

            <div class="space-y-4">
                <!-- Fecha Inicio -->
                <div>
                    <label for="export-fecha-inicio" class="block text-sm font-semibold text-gray-700 mb-2">
                        Fecha Inicio
                    </label>
                    <input type="date" id="export-fecha-inicio"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                </div>

                <!-- Fecha Fin -->
                <div>
                    <label for="export-fecha-fin" class="block text-sm font-semibold text-gray-700 mb-2">
                        Fecha Fin
                    </label>
                    <input type="date" id="export-fecha-fin"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                </div>
            </div>

            <!-- Info -->
            <div class="mt-4 p-3 bg-blue-50 rounded-xl border border-blue-100">
                <div class="flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5 text-blue-500 mt-0.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <p class="text-sm text-blue-700">
                        El reporte incluye: ventas, productos vendidos, egresos, vehículos atendidos, mecánicos y
                        facturas emitidas.
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 flex gap-3 justify-end">
            <button onclick="cerrarModalExportar()"
                class="cursor-pointer px-5 py-2.5 text-white bg-gray-700 border hover:bg-gray-100 hover:text-white border-gray-300 rounded-xl font-semibold transition-all active:scale-95">
                Cancelar
            </button>
            <button onclick="verDetallesReporte()"
                class="cursor-pointer px-5 py-2.5 text-blue-600 bg-white border border-gray-300 rounded-xl font-semibold hover:bg-blue-700 hover:text-white transition-all active:scale-95 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Detalles
            </button>
            <button onclick="descargarReporteGlobal()"
                class="cursor-pointer px-5 py-2.5 text-emerald-600 bg-white border border-gray-300 rounded-xl font-semibold hover:bg-emerald-700 hover:text-white transition-all active:scale-95 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Excel
            </button>
        </div>
    </div>
</div>
