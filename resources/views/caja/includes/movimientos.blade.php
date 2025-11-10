<div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-md font-semibold text-gray-800">Últimos <span class="block" >movimientos</span></h2>
        <a href="{{ route('venta.index.view') }}"
            class="flex gap-1 cursor-pointer text-gray-600 hover:text-gray-800 hover:underline transition-colors text-sm font-medium group">
            Ver todo el historial
            <i class="transition group-hover:translate-x-1"> 
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>

            </i>
        </a>
    </div>

    <div id="movimiento-cont" class="space-y-3 max-h-60 overflow-y-auto pr-2">
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-200 rounded-full animate-pulse"></div>
                <div>
                    <div class="h-4 bg-gray-200 rounded w-24 animate-pulse"></div>
                    <div class="h-3 bg-gray-100 rounded w-16 mt-1 animate-pulse"></div>
                </div>
            </div>
            <div class="h-4 bg-gray-200 rounded w-16 animate-pulse"></div>
        </div>

        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-200 rounded-full animate-pulse"></div>
                <div>
                    <div class="h-4 bg-gray-200 rounded w-24 animate-pulse"></div>
                    <div class="h-3 bg-gray-100 rounded w-16 mt-1 animate-pulse"></div>
                </div>
            </div>
            <div class="h-4 bg-gray-200 rounded w-16 animate-pulse"></div>
        </div>

        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-200 rounded-full animate-pulse"></div>
                <div>
                    <div class="h-4 bg-gray-200 rounded w-24 animate-pulse"></div>
                    <div class="h-3 bg-gray-100 rounded w-16 mt-1 animate-pulse"></div>
                </div>
            </div>
            <div class="h-4 bg-gray-200 rounded w-16 animate-pulse"></div>
        </div>
    </div>
</div>
