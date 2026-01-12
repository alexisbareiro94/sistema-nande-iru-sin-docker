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
     * Obtener datos del dashboard por rango de fechas personalizado
     */
    public function getDashboardDataByDateRange(string $fechaInicio, string $fechaFin): array
    {
        $inicio = Carbon::parse($fechaInicio)->startOfDay();
        $fin = Carbon::parse($fechaFin)->endOfDay();

        return [
            'resumen' => $this->getResumenMovimientos($inicio, $fin),
            'formas_pago' => $this->getFormasPago($inicio, $fin),
            'top_productos' => $this->getTopProductos($inicio, $fin),
            'cajeros_stats' => $this->getCajerosStats($inicio, $fin),
            'periodo' => 'custom',
            'fecha_inicio' => $inicio->format('d/m/Y'),
            'fecha_fin' => $fin->format('d/m/Y'),
        ];
    }

    /**
     * Resumen de movimientos: ingresos, egresos, total
     */
    public function getResumenMovimientos(Carbon $inicio, Carbon $fin): array
    {
        $movimientosStats = MovimientoCaja::whereBetween('created_at', [$inicio, $fin])
            ->selectRaw("
                COUNT(*) as total_movimientos,
                SUM(CASE WHEN tipo = 'ingreso' AND concepto NOT IN ('Apertura de caja', 'Venta', 'Venta de productos') THEN monto ELSE 0 END) as ingresos_otros,
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as total_egresos
            ")
            ->first();

        $ventas = Venta::whereBetween('created_at', [$inicio, $fin])
            ->selectRaw("
                SUM(total) as total_ventas, 
                COUNT(*) as cantidad_ventas           
            ")
            ->first();

        $total_ingresos = $ventas->total_ventas + $movimientosStats->ingresos_otros;

        return [
            'total_ingresos' => $total_ingresos,
            'total_egresos' => $movimientosStats->total_egresos,
            'balance' => $total_ingresos - $movimientosStats->total_egresos,
            'cantidad_ventas' => $ventas->cantidad_ventas,
            'cantidad_movimientos' => $movimientosStats->total_movimientos,
            'ventas' => $ventas,
        ];
    }

    /**
     * Formas de pago
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
     * Top productos más vendidos
     */
    public function getTopProductos(Carbon $inicio, Carbon $fin): array
    {
        return DetalleVenta::whereBetween('created_at', [$inicio, $fin])
            ->select('producto_id')
            ->selectRaw('SUM(cantidad) as cantidad, SUM(total) as total')
            ->with('producto:id,nombre,precio_venta')
            ->groupBy('producto_id')
            ->orderByDesc('cantidad')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'producto_id' => $item->producto_id,
                    'nombre' => $item->producto->nombre ?? 'Producto eliminado',
                    'precio' => $item->producto->precio_venta ?? 0,
                    'cantidad' => $item->cantidad,
                    'total' => $item->total,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Estadísticas por cajero
     */
    public function getCajerosStats(Carbon $inicio, Carbon $fin): array
    {
        return Venta::whereBetween('created_at', [$inicio, $fin])
            ->with('caja.user:id,name')
            ->get()->groupBy(function ($venta) {
                return $venta->caja?->user_id ?? 0;
            })
            ->map(function ($ventasCajero) {
                $cajero = $ventasCajero->first()?->caja?->user;
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
