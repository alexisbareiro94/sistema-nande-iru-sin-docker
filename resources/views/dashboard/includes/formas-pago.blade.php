<h3 class="text-lg font-semibold text-gray-800 mb-4">Formas de Pago</h3>

<div class="flex items-center justify-center">
    <div class="w-64 h-64">
        <canvas id="chart-formas-pago"></canvas>
    </div>
</div>

<!-- Leyenda -->
<div id="leyenda-formas-pago" class="mt-4 space-y-2">
    @foreach ($datos['formas_pago'] as $forma => $data)
        <div class="flex items-center justify-between text-sm">
            <div class="flex items-center gap-2">
                <span
                    class="w-3 h-3 rounded-full 
                    @if ($forma == 'efectivo') bg-emerald-500 
                    @elseif($forma == 'transferencia') bg-blue-500 
                    @else bg-purple-500 @endif">
                </span>
                <span class="capitalize text-gray-700">{{ $forma }}</span>
            </div>
            <div class="text-right">
                <span class="font-semibold text-gray-800">{{ $data['cantidad'] }}</span>
                <span class="text-gray-500 text-xs ml-1">(Gs. {{ number_format($data['monto'], 0, ',', '.') }})</span>
            </div>
        </div>
    @endforeach
</div>

<!-- Data para JS -->
<script>
    window.formasPagoData = @json($datos['formas_pago']);
</script>
