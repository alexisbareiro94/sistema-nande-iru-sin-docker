<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <!-- Título -->
    <h3 class="text-xl sm:text-2xl font-bold text-gray-800 text-center sm:text-left">
        Estadísticas de ingresos y egresos por Día
    </h3>

    <!-- Formulario de fechas -->
    <form id="dv-form-fecha"
        class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-gray-50 rounded-lg p-4 w-full sm:w-auto">
        <div class="flex flex-col gap-1 flex-1">
            <label for="periodoInicio" class="text-sm font-medium text-gray-700">Seleccionar Periodo</label>
            <select class="px-3 py-2 border border-gray-300 rounded-md w-full" name="periodoInicio" id="dv-periodo">
                <option value="semana">Semana</option>
                <option value="mes">Mes</option>
                <option value="anio">Año</option>
            </select>
        </div>

        <div class="flex flex-col gap-1 flex-1">
            <label for="dv-fecha-desde" class="text-sm font-medium text-gray-700">Desde:</label>
            <input
                class="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all w-full"
                type="date" id="dv-fecha-desde" name="dv-fecha-desde"
                value="{{ now()->startOfWeek()->format('Y-m-d') }}">
        </div>

        <div class="flex flex-col gap-1 flex-1">
            <label for="dv-fecha-hasta" class="text-sm font-medium text-gray-700">Hasta:</label>
            <input
                class="border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all w-full"
                type="date" id="dv-fecha-hasta" name="dv-fecha-hasta"
                value="{{ now()->endOfWeek()->format('Y-m-d') }}">
        </div>

        <button type="submit"
            class="cursor-pointer bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors font-medium mt-2 sm:mt-6 w-full sm:w-auto">
            Aplicar filtro
        </button>
    </form>
</div>

<!-- Gráficos -->
   <canvas id="myChart">
       <div class="flex gap-4">
           <canvas id="chartIngresos"></canvas>
           <canvas id="chartEgresos"></canvas>
       </div>
   </canvas>
