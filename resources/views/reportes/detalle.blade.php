@extends('layouts.app')
@section('titulo', 'Detalle de Reporte')
@section('ruta-actual', 'Detalle de Reporte')
@section('ruta-anterior', 'Reportes')
@section('url', '/reportes')

@section('contenido')
    <div class="p-2 md:p-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detalle de Reporte</h2>
                <p class="text-gray-600 text-sm">
                    Período: {{ $data['fechaInicio'] }} - {{ $data['fechaFin'] }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('reporte.index') }}"
                    class="cursor-pointer px-4 py-2 text-gray-600 bg-white border border-gray-300 rounded-lg font-semibold hover:bg-gray-100 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Volver
                </a>
                <a href="{{ route('reporte.exportar', ['fecha_inicio' => request('fecha_inicio'), 'fecha_fin' => request('fecha_fin')]) }}"
                    class="cursor-pointer px-4 py-2 text-white bg-emerald-600 rounded-lg font-semibold hover:bg-emerald-700 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Descargar Excel
                </a>
            </div>
        </div>

        <!-- Resumen General -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
            <!-- Total Ventas -->
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-emerald-500">
                <p class="text-sm text-gray-500 mb-1">Total Ventas</p>
                <p class="text-2xl font-bold text-gray-800">Gs.
                    {{ number_format($data['resumen']['totalVentas'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $data['resumen']['cantidadVentas'] }} ventas</p>
            </div>

            <!-- Ingresos Totales -->
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-cyan-500">
                <p class="text-sm text-gray-500 mb-1">Ingresos Totales</p>
                <p class="text-2xl font-bold text-gray-800">Gs.
                    {{ number_format($data['resumen']['totalIngresos'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Ventas + otros ingresos</p>
            </div>

            <!-- Costo Productos -->
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-orange-500">
                <p class="text-sm text-gray-500 mb-1">Costo Productos</p>
                <p class="text-2xl font-bold text-gray-800">Gs.
                    {{ number_format($data['resumen']['costoTotal'], 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Precio de compra</p>
            </div>

            <!-- Total Egresos -->
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500">
                <p class="text-sm text-gray-500 mb-1">Total Egresos</p>
                <p class="text-2xl font-bold text-gray-800">Gs.
                    {{ number_format($data['resumen']['egresos'], 0, ',', '.') }}</p>
            </div>

            <!-- Utilidad Neta -->
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
                <p class="text-sm text-gray-500 mb-1">Utilidad Neta</p>
                <p
                    class="text-2xl font-bold {{ $data['resumen']['utilidadNeta'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Gs. {{ number_format($data['resumen']['utilidadNeta'], 0, ',', '.') }}
                </p>
            </div>

            <!-- Facturas Emitidas -->
            <div class="bg-white rounded-xl shadow p-5 border-l-4 border-purple-500">
                <p class="text-sm text-gray-500 mb-1">Facturas Emitidas</p>
                <p class="text-2xl font-bold text-gray-800">{{ $data['resumen']['facturas'] }}</p>
            </div>
        </div>

        <!-- Formas de Pago -->
        <div class="bg-white rounded-xl shadow p-5 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Formas de Pago</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="size-5 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-700">Efectivo</span>
                    </div>
                    <span class="text-xl font-bold text-green-700">Gs.
                        {{ number_format($data['resumen']['totalEfectivo'], 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="size-5 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-700">Transferencia</span>
                    </div>
                    <span class="text-xl font-bold text-blue-700">Gs.
                        {{ number_format($data['resumen']['totalTransferencia'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Tabs de contenido -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <!-- Tab Headers -->
            <div class="flex border-b overflow-x-auto">
                <button onclick="showTab('ventas')" id="tab-ventas"
                    class="tab-btn px-6 py-3 font-semibold text-blue-600 border-b-2 border-blue-600 bg-blue-50">
                    Ventas ({{ count($data['ventas']) }})
                </button>
                <button onclick="showTab('productos')" id="tab-productos"
                    class="tab-btn px-6 py-3 font-semibold text-gray-500 hover:text-gray-700">
                    Productos ({{ count($data['productosVendidos']) }})
                </button>
                <button onclick="showTab('egresos')" id="tab-egresos"
                    class="tab-btn px-6 py-3 font-semibold text-gray-500 hover:text-gray-700">
                    Egresos ({{ count($data['egresosDetalle']) }})
                </button>
                <button onclick="showTab('vehiculos')" id="tab-vehiculos"
                    class="tab-btn px-6 py-3 font-semibold text-gray-500 hover:text-gray-700">
                    Vehículos ({{ count($data['vehiculos']) }})
                </button>
                <button onclick="showTab('mecanicos')" id="tab-mecanicos"
                    class="tab-btn px-6 py-3 font-semibold text-gray-500 hover:text-gray-700">
                    Mecánicos ({{ count($data['mecanicos']) }})
                </button>
                <button onclick="showTab('facturas')" id="tab-facturas"
                    class="tab-btn px-6 py-3 font-semibold text-gray-500 hover:text-gray-700">
                    Facturas ({{ count($data['facturas']) }})
                </button>
            </div>

            <!-- Tab Contents -->
            <div class="p-4">
                <!-- Ventas -->
                <div id="content-ventas" class="tab-content">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Vehículo</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Forma Pago</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($data['ventas'] as $venta)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-600">{{ $venta->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-800">
                                            {{ $venta->cliente->razon_social ?? ($venta->cliente->name ?? '-') }}</td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $venta->vehiculo ? $venta->vehiculo->patente : '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full 
                                                {{ $venta->forma_pago === 'efectivo'
                                                    ? 'bg-green-100 text-green-700'
                                                    : ($venta->forma_pago === 'transferencia'
                                                        ? 'bg-blue-100 text-blue-700'
                                                        : 'bg-yellow-100 text-yellow-700') }}">
                                                {{ ucfirst($venta->forma_pago ?? '-') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-800">Gs.
                                            {{ number_format($venta->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay ventas en
                                            este período</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Productos -->
                <div id="content-productos" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Producto</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Categoría</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Cantidad</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Total Vendido</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Utilidad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($data['productosVendidos'] as $producto)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-800 font-medium">{{ $producto['nombre'] }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $producto['categoria'] }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $producto['cantidad'] }}</td>
                                        <td class="px-4 py-3 text-right text-gray-800">Gs.
                                            {{ number_format($producto['total'], 0, ',', '.') }}</td>
                                        <td
                                            class="px-4 py-3 text-right font-semibold {{ $producto['utilidad'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                            Gs. {{ number_format($producto['utilidad'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay productos
                                            vendidos en este período</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Egresos -->
                <div id="content-egresos" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Concepto</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Responsable</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($data['egresosDetalle'] as $egreso)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-600">{{ $egreso->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-800">{{ $egreso->concepto ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $egreso->caja->user->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-red-600">Gs.
                                            {{ number_format($egreso->monto, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay egresos en
                                            este período</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Vehículos -->
                <div id="content-vehiculos" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Patente</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Marca/Modelo</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Servicios</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($data['vehiculos'] as $vehiculo)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-800 font-medium">{{ $vehiculo['patente'] }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $vehiculo['marca'] }}
                                            {{ $vehiculo['modelo'] }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $vehiculo['cliente'] }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                                                {{ $vehiculo['servicios'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay vehículos
                                            atendidos en este período</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mecánicos -->
                <div id="content-mecanicos" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Mecánico</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Total Servicios</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Pendientes</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">En Proceso</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Completados</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Cobrados</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($data['mecanicos'] as $mecanico)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-800 font-medium">{{ $mecanico['nombre'] }}</td>
                                        <td class="px-4 py-3 text-center font-semibold text-gray-800">
                                            {{ $mecanico['total'] }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">{{ $mecanico['pendientes'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">{{ $mecanico['en_proceso'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="px-2 py-1 bg-orange-100 text-orange-700 rounded-full text-xs">{{ $mecanico['completados'] }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">{{ $mecanico['cobrados'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No hay mecánicos
                                            con servicios en este período</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Facturas -->
                <div id="content-facturas" class="tab-content hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Número</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Cliente</th>
                                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Estado</th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($data['facturas'] as $factura)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-800 font-medium">
                                            {{ $factura->numero_formateado ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $factura->emision ? $factura->emision->format('d/m/Y') : '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $factura->venta->cliente->razon_social ?? ($factura->venta->cliente->name ?? '-') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full 
                                                {{ $factura->estado === 'emitida' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ ucfirst($factura->estado ?? '-') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-800">Gs.
                                            {{ number_format($factura->venta->total ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay facturas
                                            emitidas en este período</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Resetear estilos de tabs
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600', 'bg-blue-50');
                el.classList.add('text-gray-500');
            });
            // Mostrar contenido seleccionado
            document.getElementById('content-' + tabName).classList.remove('hidden');
            // Activar tab
            const activeTab = document.getElementById('tab-' + tabName);
            activeTab.classList.remove('text-gray-500');
            activeTab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600', 'bg-blue-50');
        }
    </script>
@endsection
