<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\MovimientoCaja;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\ServicioProceso;
use Carbon\Carbon;

class ReporteService
{
    // datos para los tres primeros items de reportes (ventas hoy, clientes nuevos, prod mas vendido y mas vendidos )
    public function data_index(): array
    {
        if (Producto::count() < 1) {
            return [];
        }
        $productos = Producto::orderByDesc('ventas')->limit(4)->get();
        $productoMasVendido = $productos->first();
        $productos = $productos->where('id', '!=', $productoMasVendido->id);

        $inicioMesPasado = Carbon::now()->startOfMonth()->subMonth();
        $finMesPasado = Carbon::now()->endOfDay()->subMonth();
        $ventasMesPasado = max(1, Venta::whereBetween('created_at', [$inicioMesPasado, $finMesPasado])
            ->selectRaw('SUM(total) as total')
            ->first()
            ->total);

        $inicioMes = Carbon::now()->startOfMonth();
        $fechaActual = Carbon::now()->endOfMonth();
        $ventasEsteMes = max(1, Venta::whereBetween('created_at', [$inicioMes, $fechaActual])
            ->selectRaw('SUM(total) as total')
            ->first()
            ->total);

        $usersMesPasado = max(1, User::where('role', 'cliente')->whereBetween('created_at', [$inicioMesPasado, $finMesPasado])
            ->selectRaw('COUNT(*) as count')
            ->first()
            ->count);
        $usersEsteMes = max(1, User::where('role', 'cliente')->whereBetween('created_at', [$inicioMes, $fechaActual])
            ->selectRaw('COUNT(*) as count')
            ->first()
            ->count);

        $tagUsers = '';
        $porcentajeUsers = '';
        if ($usersMesPasado != 0) {
            $valor = (($usersEsteMes - $usersMesPasado) / $usersMesPasado) * 100;
            $porcentajeUsers = round(abs($valor));
            $tagUsers = $valor >= 0 ? '+' : '-';
        } else {
            $porcentajeUsers = 0;
            $tagUsers = $usersEsteMes > 0 ? '+' : ($usersEsteMes < 0 ? '-' : '');
        }

        $porcentaje = '';
        $tag = '';
        if ($ventasMesPasado != 0) {
            $valor = (($ventasEsteMes - $ventasMesPasado) / $ventasMesPasado) * 100;
            $porcentaje = round(abs($valor));
            $tag = $valor >= 0 ? '+' : '-';
        } else {
            $porcentaje = 0;
        }

        return [
            'ventas_hoy' => [
                'saldo' => $ventasEsteMes,
                'porcentaje' => round($porcentaje),
                'tag' => $tag,
            ],
            'clientes_nuevos' => [
                'nuevos' => $usersEsteMes,
                'porcentaje' => round($porcentajeUsers),
                'tag' => $tagUsers,
            ],
            'producto_vendido' => [
                'producto' => $productoMasVendido,
                'cantidad' => $productoMasVendido->ventas,
            ],
            'productos_vendidos' => $productos,
            'utilidad' => $this->utilidad(),
        ];
    }

    public function utilidad($periodo = 'dia', $option = null)
    {
        $aperturaActual = $periodo == 'dia' ? now()->startOfDay() : ($periodo == 'semana' ? now()->startOfWeek() : now()->startOfMonth());
        $cierreActual = match ($periodo) {
            'dia' => now()->endOfDay(),
            'semana' => $option === 'hoy' ? now() : now()->endOfWeek(),
            'mes' => $option === 'hoy' ? now() : now()->endOfMonth(),
            default => now(),
        };

        $aperturaPasado = $periodo == 'dia' ? now()->startOfDay()->subDay() : ($periodo == 'semana' ? now()->startOfWeek()->subWeek() : now()->startOfMonth()->subMonth());
        $cierrePasado = match ($periodo) {
            'dia' => now()->endOfDay()->subDay(),
            'semana' => $option === 'hoy' ? now()->endOfDay()->subWeek() : now()->endOfWeek()->subWeek(),
            'mes' => $option === 'hoy' ? now()->endOfDay()->subMonth() : now()->endOfMonth()->subMonth(),
            default => now(),
        };
        $datos = [
            'actual' => [
                'total_venta' => 0,
                'ganancia' => 0,
                'descuento' => 0,
                'egreso' => 0,
                'ganancia_egreso' => 0,
                'fecha_apertura' => $aperturaActual,
                'fecha_cierre' => $cierreActual,
            ],
            'pasado' => [
                'total_venta' => 0,
                'ganancia' => 0,
                'descuento' => 0,
                'egreso' => 0,
                'ganancia_egreso' => 0,
                'fecha_apertura' => $aperturaPasado,
                'fecha_cierre' => $cierrePasado,
            ],
            'periodo' => $periodo,
            'option' => $option,
            'tag' => '',
        ];

        //ingresos de ventas
        $ventasActual = DetalleVenta::whereBetween('created_at', [$aperturaActual, $cierreActual])
            ->with('producto')
            ->get();
        $ventasPasada = DetalleVenta::whereBetween('created_at', [$aperturaPasado, $cierrePasado])
            ->with('producto')
            ->get();

        //otros ingresos
        $otrosIngresosActual = MovimientoCaja::where('concepto', '!=', 'Apertura de caja')
            ->where('concepto', '!=', 'Venta de productos')
            ->where('tipo', '!=', 'egreso')
            ->whereBetween('created_at', [$aperturaActual, $cierreActual])
            ->selectRaw('SUM(monto) as monto')
            ->first()
            ->monto;
        $datos['actual']['ganancia'] = $otrosIngresosActual;

        $otrosIngresosPasado = MovimientoCaja::where('concepto', '!=', 'Apertura de caja')
            ->where('concepto', '!=', 'Venta de productos')
            ->where('tipo', '!=', 'egreso')
            ->whereBetween('created_at', [$aperturaPasado, $cierrePasado])
            ->selectRaw('SUM(monto) as monto')
            ->first()
            ->monto;
        $datos['pasado']['ganancia'] = $otrosIngresosPasado;

        //egresos
        $egresosActual = MovimientoCaja::where('tipo', 'egreso')
            ->whereBetween('created_at', [$aperturaActual, $cierreActual])
            ->selectRaw('SUM(monto) as monto')
            ->first()
            ->monto;

        $egresosPasada = MovimientoCaja::where('tipo', 'egreso')
            ->whereBetween('created_at', [$aperturaPasado, $cierrePasado])
            ->selectRaw('SUM(monto) as monto')
            ->first()
            ->monto;

        $datos['actual']['total_venta'] = $ventasActual->sum('total');
        $datos['pasado']['total_venta'] = $ventasPasada->sum('total');
        foreach ($ventasActual as $venta) {
            $datos['actual']['descuento'] += (($venta->producto->precio_compra ?? 0) * $venta->cantidad);
        }
        foreach ($ventasPasada as $venta) {
            $datos['pasado']['descuento'] += (($venta->producto->precio_compra ?? 0) * $venta->cantidad);
        }

        $datos['actual']['ganancia'] = ($datos['actual']['total_venta'] + $datos['actual']['ganancia']) - $datos['actual']['descuento'];
        $datos['pasado']['ganancia'] = ($datos['pasado']['total_venta'] + $datos['pasado']['ganancia']) - $datos['pasado']['descuento'];

        $actual = $datos['actual']['ganancia'];
        $pasado = $datos['pasado']['ganancia'];

        if ($pasado != 0) {
            $valor = (($actual - $pasado) / abs($pasado)) * 100;
            $porcentaje = round(abs($valor));
            $datos['tag'] = $valor >= 0 ? '+' : '-';
        } else {
            $porcentaje = 0;
            $datos['tag'] = $actual > 0 ? '+' : ($actual < 0 ? '-' : '');
        }

        $diferencia = $actual - $pasado;
        $datos['diferencia'] = $diferencia;
        $datos['porcentaje'] = $porcentaje;

        $datos['actual']['egreso'] = $egresosActual;
        $datos['actual']['ganancia_egreso'] = $datos['actual']['ganancia'] - $datos['actual']['egreso'];

        $datos['pasado']['egreso'] = $egresosPasada;
        $datos['pasado']['ganancia_egreso'] = $datos['pasado']['ganancia'] - $datos['pasado']['egreso'];

        $diferencia_egreso = $datos['actual']['ganancia_egreso'] - $datos['pasado']['ganancia_egreso'];
        $datos['diferencia_egreso'] = $diferencia_egreso;


        $actualEgreso = $datos['actual']['ganancia_egreso'];
        $pasadoEgreso = $datos['pasado']['ganancia_egreso'];

        if ($pasadoEgreso != 0) {
            $raw = (($actualEgreso - $pasadoEgreso) / abs($pasadoEgreso)) * 100;
            $porcentaje_egreso = round(abs($raw));
            $datos['tagE'] = $raw >= 0 ? '+' : '-';
        } else {
            $porcentaje_egreso = 0;
            $datos['tagE'] = $actualEgreso > 0 ? '+' : ($actualEgreso < 0 ? '-' : '');
        }
        $datos['porcentaje_egreso'] = $porcentaje_egreso;

        return $datos;
    }

    /**
     * Calcula la utilidad para un rango de fechas personalizado
     * Incluye comparación con el período anterior de la misma duración
     */
    public function utilidadPersonalizada(string $fechaInicio, string $fechaFin): array
    {
        $aperturaActual = Carbon::parse($fechaInicio)->startOfDay();
        $cierreActual = Carbon::parse($fechaFin)->endOfDay();

        // Calcular la duración del período en días
        $duracionDias = $aperturaActual->diffInDays($cierreActual) + 1;

        // Calcular el período anterior con la misma duración
        $cierrePasado = $aperturaActual->copy()->subDay()->endOfDay();
        $aperturaPasado = $cierrePasado->copy()->subDays($duracionDias - 1)->startOfDay();

        $datos = [
            'actual' => [
                'total_venta' => 0,
                'ganancia' => 0,
                'descuento' => 0,
                'egreso' => 0,
                'ganancia_egreso' => 0,
                'fecha_apertura' => $aperturaActual,
                'fecha_cierre' => $cierreActual,
            ],
            'pasado' => [
                'total_venta' => 0,
                'ganancia' => 0,
                'descuento' => 0,
                'egreso' => 0,
                'ganancia_egreso' => 0,
                'fecha_apertura' => $aperturaPasado,
                'fecha_cierre' => $cierrePasado,
            ],
            'periodo' => 'personalizado',
            'tag' => '',
        ];

        // =============== PERÍODO ACTUAL ===============
        //ingresos de ventas
        $ventasActual = DetalleVenta::whereBetween('created_at', [$aperturaActual, $cierreActual])
            ->with('producto')
            ->get();

        //otros ingresos
        $otrosIngresosActual = MovimientoCaja::where('concepto', '!=', 'Apertura de caja')
            ->where('concepto', '!=', 'Venta de productos')
            ->where('tipo', '!=', 'egreso')
            ->whereBetween('created_at', [$aperturaActual, $cierreActual])
            ->selectRaw('SUM(monto) as monto')
            ->first()
            ->monto;
        $datos['actual']['ganancia'] = $otrosIngresosActual;

        //egresos
        $egresosActual = MovimientoCaja::where('tipo', 'egreso')
            ->whereBetween('created_at', [$aperturaActual, $cierreActual])
            ->selectRaw('SUM(monto) as monto')
            ->first()
            ->monto;

        $datos['actual']['total_venta'] = $ventasActual->sum('total');
        foreach ($ventasActual as $venta) {
            $datos['actual']['descuento'] += (($venta->producto->precio_compra ?? 0) * $venta->cantidad);
        }

        $datos['actual']['ganancia'] = ($datos['actual']['total_venta'] + $datos['actual']['ganancia']) - $datos['actual']['descuento'];
        $datos['actual']['egreso'] = $egresosActual;
        $datos['actual']['ganancia_egreso'] = $datos['actual']['ganancia'] - ($datos['actual']['egreso'] ?? 0);

        // =============== PERÍODO PASADO ===============
        $ventasPasado = DetalleVenta::whereBetween('created_at', [$aperturaPasado, $cierrePasado])
            ->with('producto')
            ->get();

        $otrosIngresosPasado = MovimientoCaja::where('concepto', '!=', 'Apertura de caja')
            ->where('concepto', '!=', 'Venta de productos')
            ->where('tipo', '!=', 'egreso')
            ->whereBetween('created_at', [$aperturaPasado, $cierrePasado])
            ->selectRaw('SUM(monto) as monto')
            ->first()
            ->monto;
        $datos['pasado']['ganancia'] = $otrosIngresosPasado;

        $egresosPasado = MovimientoCaja::where('tipo', 'egreso')
            ->whereBetween('created_at', [$aperturaPasado, $cierrePasado])
            ->selectRaw('SUM(monto) as monto')
            ->first()
            ->monto;

        $datos['pasado']['total_venta'] = $ventasPasado->sum('total');
        foreach ($ventasPasado as $venta) {
            $datos['pasado']['descuento'] += (($venta->producto->precio_compra ?? 0) * $venta->cantidad);
        }

        $datos['pasado']['ganancia'] = ($datos['pasado']['total_venta'] + $datos['pasado']['ganancia']) - $datos['pasado']['descuento'];
        $datos['pasado']['egreso'] = $egresosPasado;
        $datos['pasado']['ganancia_egreso'] = $datos['pasado']['ganancia'] - ($datos['pasado']['egreso'] ?? 0);

        // =============== CALCULAR DIFERENCIAS Y PORCENTAJES ===============
        $actual = $datos['actual']['ganancia'];
        $pasado = $datos['pasado']['ganancia'];

        if ($pasado != 0) {
            $valor = (($actual - $pasado) / abs($pasado)) * 100;
            $porcentaje = round(abs($valor));
            $datos['tag'] = $valor >= 0 ? '+' : '-';
        } else {
            $porcentaje = 0;
            $datos['tag'] = $actual > 0 ? '+' : ($actual < 0 ? '-' : '');
        }

        $diferencia = $actual - $pasado;
        $datos['diferencia'] = $diferencia;
        $datos['porcentaje'] = $porcentaje;

        // Calcular porcentaje y diferencia considerando egresos
        $actualEgreso = $datos['actual']['ganancia_egreso'];
        $pasadoEgreso = $datos['pasado']['ganancia_egreso'];

        if ($pasadoEgreso != 0) {
            $raw = (($actualEgreso - $pasadoEgreso) / abs($pasadoEgreso)) * 100;
            $porcentaje_egreso = round(abs($raw));
            $datos['tagE'] = $raw >= 0 ? '+' : '-';
        } else {
            $porcentaje_egreso = 0;
            $datos['tagE'] = $actualEgreso > 0 ? '+' : ($actualEgreso < 0 ? '-' : '');
        }
        $datos['porcentaje_egreso'] = $porcentaje_egreso;
        $datos['diferencia_egreso'] = $actualEgreso - $pasadoEgreso;

        return $datos;
    }

    public function gananacias_data($periodo): array
    {
        $hoy = now()->endOfDay();
        $desde = now()->startOfDay()->subDay($periodo);

        $datos = [];

        $ventas = DetalleVenta::whereBetween('created_at', [$desde, $hoy])
            ->with('producto')
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($query) {
                return Carbon::parse($query->created_at)->format('Y-m-d');
            });

        $egresos = MovimientoCaja::whereBetween('created_at', [$desde, $hoy])
            ->where('tipo', 'egreso')
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as monto')
            ->groupBy('fecha')
            ->get()
            ->pluck('monto', 'fecha');


        $otrosIngresos = MovimientoCaja::where('concepto', '!=', 'Apertura de caja')
            ->where('concepto', '!=', 'Venta de productos')
            ->where('tipo', '!=', 'egreso')
            ->whereBetween('created_at', [$desde, $hoy])
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as monto')
            ->groupBy('fecha')
            ->get()
            ->pluck('monto', 'fecha');

        $index = 0;
        foreach ($ventas as $fecha => $detalles) {
            $total = $detalles->sum('total');
            $datos[$index] = [
                'fecha' => $fecha,
                'ganancia' => 0,
                'total_fecha' => $total,
                'descuento' => 0,
                'egresos' => 0,
                'ganacia_egresos' => 0,
            ];
            foreach ($detalles as $detalle) {
                $datos[$index]['descuento'] += ($detalle->producto->precio_compra * $detalle->cantidad) ?? 0;
            }
            $datos[$index]['ganancia'] = ($datos[$index]['total_fecha'] - $datos[$index]['descuento']) ?? 0;
            if (!empty($egresos[$fecha])) {
                $datos[$index]['egresos'] = ($egresos[$fecha]) ?? 0;
                $datos[$index]['ganacia_egresos'] = ($datos[$index]['ganancia'] - $datos[$index]['egresos']) ?? 0;
            }
            $index++;
        }
        $labels = $ventas->keys()->map(function ($fecha) {
            return date('d-m', strtotime($fecha));
        });

        foreach ($otrosIngresos as $index => $monto) {
            foreach ($datos as $i => $dato) {
                if ($dato['fecha'] == $index) {
                    $datos[$i]['ganancia'] += $monto;
                    $datos[$i]['ganacia_egresos'] += $monto;
                }
            }
        }

        return [
            'labels' => $labels,
            'datos' => $datos,
        ];
    }

    public function data_detalle_reporte(object $request)
    {
        $fechaInicioStr = $request->query('fecha_inicio', now()->startOfWeek()->format('Y-m-d'));
        $fechaFinStr = $request->query('fecha_fin', now()->format('Y-m-d'));

        $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
        $fechaFin = Carbon::parse($fechaFinStr)->endOfDay();

        // Ventas
        $ventas = Venta::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with(['cliente', 'vehiculo', 'detalleVentas.producto'])
            ->orderBy('created_at', 'desc')
            ->get();

        $otrosIngresos = MovimientoCaja::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('concepto', '!=', 'Apertura de caja')
            ->where('concepto', '!=', 'Venta de productos')
            ->where('tipo', '!=', 'egreso')
            ->selectRaw('SUM(monto) as monto')
            ->first()
            ->monto;

        // Calcular resumen
        $totalVentas = $ventas->sum('total');
        $cantidadVentas = $ventas->count();

        // Detalles para productos y costo
        $detalles = DetalleVenta::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with('producto')
            ->get();

        $costoTotal = 0;
        foreach ($detalles as $detalle) {
            $costoTotal += ($detalle->producto->precio_compra ?? 0) * $detalle->cantidad;
        }

        // Productos vendidos agrupados
        $productosAgrupados = $detalles->groupBy('producto_id');
        $productosVendidos = [];
        foreach ($productosAgrupados as $productoId => $items) {
            $producto = $items->first()->producto;
            if (!$producto)
                continue;
            $cantidadTotal = $items->sum('cantidad');
            $totalVendido = $items->sum('total');
            $costo = ($producto->precio_compra ?? 0) * $cantidadTotal;
            $productosVendidos[] = [
                'nombre' => $producto->nombre,
                'categoria' => $producto->tipo ?? '-',
                'cantidad' => $cantidadTotal,
                'total' => $totalVendido,
                'utilidad' => $totalVendido - $costo,
            ];
        }
        usort($productosVendidos, fn($a, $b) => $b['cantidad'] <=> $a['cantidad']);

        // Egresos
        $egresosDetalle = MovimientoCaja::where('tipo', 'egreso')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with('caja.user')
            ->orderBy('created_at', 'desc')
            ->get();
        $egresos = $egresosDetalle->sum('monto');

        $utilidadBruta = ($totalVentas + $otrosIngresos) - $costoTotal;
        $utilidadNeta = $utilidadBruta - $egresos;

        // Formas de pago
        $ventaIds = $ventas->pluck('id');
        $pagos = Pago::whereIn('venta_id', $ventaIds)->get();
        $pagosPorMetodo = $pagos->groupBy('metodo');
        $totalEfectivo = $pagosPorMetodo->get('efectivo', collect())->sum('monto');
        $totalTransferencia = $pagosPorMetodo->get('transferencia', collect())->sum('monto');

        // Servicios/Vehículos
        $servicios = ServicioProceso::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with(['vehiculo.cliente', 'mecanico'])
            ->get();

        $vehiculosAgrupados = $servicios->groupBy('vehiculo_id');
        $vehiculos = [];
        foreach ($vehiculosAgrupados as $vehiculoId => $items) {
            $vehiculo = $items->first()->vehiculo;
            if (!$vehiculo)
                continue;
            $vehiculos[] = [
                'patente' => $vehiculo->patente ?? '-',
                'marca' => $vehiculo->marca ?? '-',
                'modelo' => $vehiculo->modelo ?? '-',
                'cliente' => $vehiculo->cliente->razon_social ?? $vehiculo->cliente->name ?? '-',
                'servicios' => $items->count(),
            ];
        }

        // Mecánicos
        $mecanicosAgrupados = $servicios->groupBy('mecanico_id');
        $mecanicos = [];
        foreach ($mecanicosAgrupados as $mecanicoId => $items) {
            $mecanico = $items->first()->mecanico;
            if (!$mecanico)
                continue;
            $mecanicos[] = [
                'nombre' => $mecanico->name ?? '-',
                'total' => $items->count(),
                'pendientes' => $items->where('estado', 'pendiente')->count(),
                'en_proceso' => $items->where('estado', 'en_proceso')->count(),
                'completados' => $items->where('estado', 'completado')->count(),
                'cobrados' => $items->where('estado', 'cobrado')->count(),
            ];
        }
        usort($mecanicos, fn($a, $b) => $b['total'] <=> $a['total']);

        // Facturas
        $facturas = Factura::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->with('venta.cliente')
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'fechaInicio' => $fechaInicio->format('d/m/Y'),
            'fechaFin' => $fechaFin->format('d/m/Y'),
            'resumen' => [
                'totalVentas' => $totalVentas,
                'cantidadVentas' => $cantidadVentas,
                'egresos' => $egresos,
                'utilidadNeta' => $utilidadNeta,
                'facturas' => $facturas->count(),
                'totalEfectivo' => $totalEfectivo,
                'totalTransferencia' => $totalTransferencia,
            ],
            'ventas' => $ventas,
            'productosVendidos' => $productosVendidos,
            'egresosDetalle' => $egresosDetalle,
            'vehiculos' => $vehiculos,
            'mecanicos' => $mecanicos,
            'facturas' => $facturas,
        ];
    }
}
