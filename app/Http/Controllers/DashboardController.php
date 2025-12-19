<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService)
    {
    }

    public function index()
    {
        $datos = $this->dashboardService->getDashboardData(7);

        return view('dashboard.index', [
            'datos' => $datos,
        ]);
    }

    public function stats(string $periodo)
    {
        try {
            $datos = $this->dashboardService->getDashboardData((int) $periodo);

            return response()->json([
                'success' => true,
                'datos' => $datos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function movimientosDia(string $periodo)
    {
        try {
            $inicio = now()->startOfDay()->subDays((int) $periodo);
            $fin = now()->endOfDay();

            $datos = $this->dashboardService->getMovimientosPorDia($inicio, $fin);

            return response()->json([
                'success' => true,
                'datos' => $datos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener estadísticas por rango de fechas personalizado
     */
    public function statsByDateRange(Request $request)
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            ]);

            $datos = $this->dashboardService->getDashboardDataByDateRange($request->fecha_inicio, $request->fecha_fin);

            return response()->json([
                'success' => true,
                'datos' => $datos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
