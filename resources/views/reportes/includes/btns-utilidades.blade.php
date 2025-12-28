<div class="flex flex-wrap items-center gap-6 md:max-w-[1600px] max-w-[300px]">
    <!-- Grupo de botones de rango de tiempo -->
    <div>
        <label class="text-sm font-medium text-gray-600 mb-1 block">Periodo</label>
        <div class="flex space-x-2 bg-gray-300 rounded-lg">
            <button id="7dp" data-utilidad="dia"
                class="utilidad-btn cursor-pointer text-xs px-3 py-1 transition-all duration-300 ease-in-out border border-gray-300 bg-gray-50 font-semibold rounded-md shadow-lg">
                Diario
            </button>
            <button id="30dp" data-utilidad="semana"
                class="utilidad-btn cursor-pointer text-xs px-3 py-1 transition-all duration-300 ease-in-out border border-gray-300 bg-gray-300 font-semibold rounded-md">
                Semana
            </button>
            <button id="90dp" data-utilidad="mes"
                class="utilidad-btn cursor-pointer text-xs px-3 py-1 transition-all duration-300 ease-in-out border border-gray-300 bg-gray-300 font-semibold rounded-md">
                Mensual
            </button>
            <button id="customp" data-utilidad="personalizado"
                class="utilidad-btn cursor-pointer text-xs px-3 py-1 transition-all duration-300 ease-in-out border border-gray-300 bg-gray-300 font-semibold rounded-md">
                Personalizado
            </button>
        </div>
    </div>

    <!-- Grupo de fechas personalizadas -->
    <div id="fechas-personalizadas" class="hidden">
        <label class="text-sm font-medium text-gray-600 mb-1 block">Rango de Fechas</label>
        <div class="block space-y-2 items-center space-x-2">
            <input type="date" id="fecha-inicio"
                class="text-xs px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <span class="text-gray-500 text-xs">hasta</span>
            <input type="date" id="fecha-fin"
                class="text-xs px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button id="btn-aplicar-fechas"
                class="cursor-pointer text-xs px-3 py-1 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 transition-all duration-300">
                Aplicar
            </button>
        </div>
    </div>

    <!-- Grupo de botones de comparación -->
    <div>
        <label class="text-sm font-medium text-gray-600 mb-1 block">Comparar con hoy</label>
        <div class="flex space-x-2 bg-gray-300 rounded-lg max-w-[90px]">
            <button
                class="option-btn cursor-pointer text-xs px-3 py-1 transition-all duration-300 ease-in-out border border-gray-300 bg-gray-50 font-semibold rounded-md shadow-lg">
                NO
            </button>
            <button data-option="hoy"
                class="option-btn cursor-pointer text-xs px-4 py-1 transition-all duration-300 ease-in-out border border-gray-300 bg-gray-300 font-semibold rounded-md">
                SI
            </button>
        </div>
    </div>

    <!-- Grupo de botones de egreso -->
    <div>
        <label class="text-sm font-medium text-gray-600 mb-1 block">Restar Egresos</label>
        <div class="flex space-x-2 bg-gray-300 rounded-lg">
            <button
                class="regreso-btn cursor-pointer text-xs px-3 py-1 transition-all duration-300 ease-in-out border border-gray-300 bg-gray-50 font-semibold rounded-md shadow-lg">
                NO
            </button>
            <button data-regreso="true"
                class="regreso-btn cursor-pointer text-xs px-4 py-1 transition-all duration-300 ease-in-out border border-gray-300 bg-gray-300 font-semibold rounded-md">
                SI
            </button>
        </div>
    </div>
</div>
