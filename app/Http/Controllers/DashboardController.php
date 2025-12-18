<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

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
}
