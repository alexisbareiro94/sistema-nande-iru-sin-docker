<h3 class="text-lg font-semibold text-gray-800 mb-4">Rendimiento por Cajero</h3>

<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200 bg-gray-50">
                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600">Cajero</th>
                <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600">Ventas</th>
                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-600">Total Vendido</th>
                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-600">Promedio</th>
                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-600">Mayor Venta</th>
            </tr>
        </thead>
        <tbody id="tabla-cajeros">
            @forelse($datos['cajeros_stats'] as $cajero)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($cajero['nombre'], 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-800">{{ $cajero['nombre'] }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                            {{ $cajero['cantidad_ventas'] }}
                        </span>
                    </td>
                    <td class="py-4 px-4 text-right font-bold text-emerald-600">
                        Gs. {{ number_format($cajero['total_ventas'], 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-4 text-right text-gray-700">
                        Gs. {{ number_format($cajero['promedio_venta'], 0, ',', '.') }}
                    </td>
                    <td class="py-4 px-4 text-right text-gray-700">
                        Gs. {{ number_format($cajero['mayor_venta'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500">
                        No hay datos de cajeros en este período
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
