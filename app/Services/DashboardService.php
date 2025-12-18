<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\{Caja, DetalleVenta, MovimientoCaja, Pago, Producto, User, Venta};
use Carbon\Carbon;

class DashboardService
{
    /**
     * Obtener todos los datos del dashboard
     */
    public function getDashboardData(int $periodo = 7): array
    {
        $inicio = now()->startOfDay()->subDays($periodo);
        $fin = now()->endOfDay();

        return [
            'resumen' => $this->getResumenMovimientos($inicio, $fin),
            'formas_pago' => $this->getFormasPago($inicio, $fin),
            'top_productos' => $this->getTopProductos($inicio, $fin),
            'cajeros_stats' => $this->getCajerosStats($inicio, $fin),
            'periodo' => $periodo,
            'fecha_inicio' => $inicio->format('d/m/Y'),
            'fecha_fin' => $fin->format('d/m/Y'),
        ];
    }

    /**
     * Resumen de movimientos: ingresos, egresos, total
     */
    public function getResumenMovimientos(Carbon $inicio, Carbon $fin): array
    {
        $movimientos = MovimientoCaja::whereBetween('created_at', [$inicio, $fin])->get();

        $ingresos = $movimientos->where('tipo', 'ingreso')
            ->where('concepto', '!=', 'Apertura de caja')
            ->sum('monto');

        $egresos = $movimientos->where('tipo', 'egreso')->sum('monto');

        $ventas = Venta::whereBetween('created_at', [$inicio, $fin])->get();
        $totalVentas = $ventas->sum('total');
        $cantidadVentas = $ventas->count();

        return [
            'total_ingresos' => $ingresos + $totalVentas,
            'total_egresos' => $egresos,
            'balance' => ($ingresos + $totalVentas) - $egresos,
            'cantidad_ventas' => $cantidadVentas,
            'cantidad_movimientos' => $movimientos->count(),
        ];
    }

    /**
     * Distribución de formas de pago
     */
    public function getFormasPago(Carbon $inicio, Carbon $fin): array
    {
        $ventas = Venta::whereBetween('created_at', [$inicio, $fin])
            ->get()
            ->groupBy('forma_pago');

        $resultado = [];
        foreach ($ventas as $formaPago => $ventasGrupo) {
            $resultado[$formaPago] = [
                'cantidad' => $ventasGrupo->count(),
                'monto' => $ventasGrupo->sum('total'),
            ];
        }

        return $resultado;
    }

    /**
     * Top 10 productos más vendidos
     */
    public function getTopProductos(Carbon $inicio, Carbon $fin): array
    {
        return DetalleVenta::whereBetween('created_at', [$inicio, $fin])
            ->with('producto:id,nombre,precio_venta')
            ->get()
            ->groupBy('producto_id')
            ->map(function ($items) {
                $producto = $items->first()->producto;
                return [
                    'producto_id' => $items->first()->producto_id,
                    'nombre' => $producto->nombre ?? 'Producto eliminado',
                    'precio' => $producto->precio_venta ?? 0,
                    'cantidad' => $items->sum('cantidad'),
                    'total' => $items->sum('total'),
                ];
            })
            ->sortByDesc('cantidad')
            ->take(10)
            ->values()
            ->toArray();
    }

    /**
     * Estadísticas por cajero
     */
    public function getCajerosStats(Carbon $inicio, Carbon $fin): array
    {
        $ventas = Venta::whereBetween('created_at', [$inicio, $fin])
            ->with('caja.user:id,name')
            ->get();

        return $ventas->groupBy(function ($venta) {
            return $venta->caja?->user_id ?? 0;
        })
            ->map(function ($ventasCajero) {
                $cajero = $ventasCajero->first()->caja?->user;
                return [
                    'cajero_id' => $cajero?->id ?? 0,
                    'nombre' => $cajero?->name ?? 'Sin asignar',
                    'cantidad_ventas' => $ventasCajero->count(),
                    'total_ventas' => $ventasCajero->sum('total'),
                    'promedio_venta' => $ventasCajero->count() > 0
                        ? round($ventasCajero->sum('total') / $ventasCajero->count(), 0)
                        : 0,
                    'mayor_venta' => $ventasCajero->max('total'),
                ];
            })
            ->sortByDesc('total_ventas')
            ->values()
            ->toArray();
    }

    /**
     * Obtener datos de movimientos agrupados por día
     */
    public function getMovimientosPorDia(Carbon $inicio, Carbon $fin): array
    {
        $movimientos = MovimientoCaja::whereBetween('created_at', [$inicio, $fin])
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($mov) {
                return Carbon::parse($mov->created_at)->format('Y-m-d');
            });

        $datos = [];
        foreach ($movimientos as $fecha => $movsDia) {
            $datos[] = [
                'fecha' => $fecha,
                'ingresos' => $movsDia->where('tipo', 'ingreso')->where('concepto', '!=', 'Apertura de caja')->sum('monto'),
                'egresos' => $movsDia->where('tipo', 'egreso')->sum('monto'),
            ];
        }

        return $datos;
    }
}
