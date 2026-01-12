<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Models\{MovimientoCaja, Venta, DetalleVenta};
use App\Services\ReporteService;
use App\Exports\ReporteGlobalExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function __construct(protected ReporteService $reporteService)
    {
    }

    public function index(): View
    {
        $datos = $this->reporteService->data_index();
        return view('reportes.index', [
            'data' => $datos ?? ''
        ]);
    }

    public function tipos_pagos(string $periodo): JsonResponse
    {
        try {
            $inicio = now()->startOfDay()->subDay($periodo);
            $hoy = now()->endOfDay();
            $pagos = Venta::whereBetween('created_at', [$inicio, $hoy])
                ->selectRaw('forma_pago, COUNT(*) as total')
                ->groupBy('forma_pago')
                ->get()
                ->pluck('total', 'forma_pago');

            $ingresos = Venta::whereBetween('created_at', [$inicio, $hoy])
                ->selectRaw('forma_pago, SUM(total) as total')
                ->groupBy('forma_pago')
                ->get()
                ->pluck('total', 'forma_pago')
                ->toArray();

            $labels = $pagos->keys();
            $mixto = $pagos['mixto'] ?? 0;
            $transferencia = $pagos['transferencia'] ?? 0;
            $efectivo = $pagos['efectivo'] ?? 0;

            return response()->json([
                'success' => true,
                'labels' => $labels,
                'mixto' => $mixto,
                'transferencia' => $transferencia,
                'efectivo' => $efectivo,
                'ingresos' => $ingresos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param
     * periodo: 7, 30, 90, representa el rango de fechas
     */
    public function ventas_chart(string $periodo): JsonResponse
    {
        try {
            $inicio = now()->startOfDay()->subDay($periodo);
            $hoy = now()->endOfDay();

            $ventas = Venta::whereBetween('created_at', [$inicio, $hoy])
                ->selectRaw('DATE(created_at) as fecha, SUM(total) as total_venta')
                ->groupBy('fecha')
                ->orderBy('fecha', 'asc')
                ->get()
                ->pluck('total_venta', 'fecha');

            $labels = $ventas->keys();

            return response()->json([
                'success' => true,
                'labels' => $labels,
                'ventas' => $ventas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param
     * periodo= 7, 30 o 90, representa el rango que se va a medir
     */
    public function tipo_venta(string $periodo): JsonResponse
    {
        try {
            $inicio = now()->startOfDay()->subDay($periodo);
            $hoy = now()->endOfDay();

            $ventas = DetalleVenta::whereBetween('ventas.created_at', [$inicio, $hoy])
                ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
                ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
                ->selectRaw("
                    COUNT(CASE WHEN productos.tipo = 'producto' THEN 1 END) as productos,
                    COUNT(CASE WHEN productos.tipo = 'servicio' THEN 1 END) as servicios,
                    SUM(CASE WHEN productos.tipo = 'producto' THEN ventas.total ELSE 0 END) as ingresos_producto,
                    SUM(CASE WHEN productos.tipo = 'servicio' THEN ventas.total ELSE 0 END) as ingresos_servicio
                ")
                ->first();

            $conteo = [
                'producto' => $ventas->productos,
                'servicio' => $ventas->servicios,
                'ingresos' => [
                    'producto' => $ventas->ingresos_producto,
                    'servicio' => $ventas->ingresos_servicio,
                ],
            ];
            $labels = array_keys($conteo);

            return response()->json([
                'success' => true,
                'labels' => $labels,
                'conteo' => $conteo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    /**
     * @params
     * function= para la comparativa por dia, semana o mes
     * opción = seria para comparar con la semana o mes completa anterior (== null) o igualar al dia de hoy (== hoy)
     * egreso= le resta los egresos
     */
    public function tendencia(string $periodo, ?string $opcion = null)
    {
        try {
            $data = $this->reporteService->utilidad($periodo, $opcion);
            $data['actual']['fecha_apertura'] = Carbon::parse($data['actual']['fecha_apertura'])->format('d-m');
            $data['actual']['fecha_cierre'] = Carbon::parse($data['actual']['fecha_cierre'])->format('d-m');

            $data['pasado']['fecha_apertura'] = Carbon::parse($data['pasado']['fecha_apertura'])->format('d-m');
            $data['pasado']['fecha_cierre'] = Carbon::parse($data['pasado']['fecha_cierre'])->format('d-m');

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function gananacias(string $periodo)
    {
        try {
            $data = $this->reporteService->gananacias_data($periodo);
            return response()->json([
                'labels' => $data['labels'],
                'datos' => $data['datos'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Endpoint para calcular utilidad con fechas personalizadas
     */
    public function tendenciaPersonalizada(Request $request)
    {
        try {
            $fechaInicio = $request->query('fecha_inicio');
            $fechaFin = $request->query('fecha_fin');

            if (!$fechaInicio || !$fechaFin) {
                return response()->json([
                    'success' => false,
                    'error' => 'Debe proporcionar fecha_inicio y fecha_fin',
                ], 400);
            }

            $data = $this->reporteService->utilidadPersonalizada($fechaInicio, $fechaFin);
            $data['actual']['fecha_apertura'] = Carbon::parse($data['actual']['fecha_apertura'])->format('d-m');
            $data['actual']['fecha_cierre'] = Carbon::parse($data['actual']['fecha_cierre'])->format('d-m');

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function egresos(string $periodo)
    {
        try {
            $inicio = now()->startOfDay()->subDay($periodo);
            $hoy = now()->endOfDay();
            $movs = MovimientoCaja::where('tipo', 'egreso')
                ->whereBetween('created_at', [$inicio, $hoy])
                ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
                ->groupBy('fecha')
                ->get()
                ->pluck('total', 'fecha');

            return response()->json([
                'success' => true,
                'labels' => $movs->keys(),
                'egresos' => $movs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function egresos_concepto(string $periodo)
    {
        try {
            $inicio = now()->startOfDay()->subDay($periodo);
            $hoy = now()->endOfDay();

            $movs = MovimientoCaja::where('tipo', 'egreso')
                ->whereBetween('created_at', [$inicio, $hoy])
                ->selectRaw('concepto, SUM(monto) as total')
                ->groupBy('concepto')
                ->get()
                ->pluck('total', 'concepto');

            return response()->json([
                'success' => true,
                'egresos' => $movs,
                'labels' => $movs->keys(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Exportar reporte global a Excel
     */
    public function exportarGlobal(Request $request)
    {
        $fechaInicio = $request->query('fecha_inicio', now()->startOfWeek()->format('Y-m-d'));
        $fechaFin = $request->query('fecha_fin', now()->format('Y-m-d'));

        $nombreArchivo = 'reporte_global_' . $fechaInicio . '_a_' . $fechaFin . '.xlsx';

        return Excel::download(new ReporteGlobalExport($fechaInicio, $fechaFin), $nombreArchivo);
    }

    /**
     * Vista detallada del reporte
     */
    public function detalleReporte(Request $request)
    {
        $data = $this->reporteService->data_detalle_reporte($request);
        return view('reportes.detalle', [
            'data' => $data
        ]);
    }
}
