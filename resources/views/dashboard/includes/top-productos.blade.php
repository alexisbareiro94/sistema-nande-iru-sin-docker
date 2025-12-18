<h3 class="text-lg font-semibold text-gray-800 mb-4">Top 10 Productos Más Vendidos</h3>

<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-gray-200">
                <th class="text-left py-3 px-2 text-sm font-semibold text-gray-600">#</th>
                <th class="text-left py-3 px-2 text-sm font-semibold text-gray-600">Producto</th>
                <th class="text-center py-3 px-2 text-sm font-semibold text-gray-600">Cantidad</th>
                <th class="text-right py-3 px-2 text-sm font-semibold text-gray-600">Total</th>
            </tr>
        </thead>
        <tbody id="tabla-top-productos">
            @forelse($datos['top_productos'] as $index => $producto)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="py-3 px-2">
                        <span
                            class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                            @if ($index == 0) bg-yellow-100 text-yellow-700
                            @elseif($index == 1) bg-gray-200 text-gray-700
                            @elseif($index == 2) bg-amber-100 text-amber-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ $index + 1 }}
                        </span>
                    </td>
                    <td class="py-3 px-2">
                        <span class="font-medium text-gray-800">{{ $producto['nombre'] }}</span>
                    </td>
                    <td class="py-3 px-2 text-center">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $producto['cantidad'] }} uds
                        </span>
                    </td>
                    <td class="py-3 px-2 text-right font-semibold text-gray-800">
                        Gs. {{ number_format($producto['total'], 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-500">
                        No hay productos vendidos en este período
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
