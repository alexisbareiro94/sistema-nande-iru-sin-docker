<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbrirCajaRequest;
use App\Http\Requests\UpdateCajaRequest;
use App\Models\{Caja, MovimientoCaja, Venta, DetalleVenta, Pago, PagoSalario, User, Auditoria};
use App\Services\CajaService;

class CajaController extends Controller
{
    public function __construct(protected CajaService $cajaService)
    {
        crear_caja();
    }
    public function index_view()
    {
        $data = $this->cajaService->index_view_data();
        return view("caja.index", [
            "caja" => $data["caja"],
            'pagosSalario' => $data["pagosSalario"],
            'users' => $data["users"],
        ]);
    }

    public function abrir(AbrirCajaRequest $request)
    {
        $res = $request->validated();
        $this->cajaService->abrir_data($res);
        return back()->with("success", "Caja Abierta Correctamente");
    }

    //cuando se cierra la caja
    public function update(UpdateCajaRequest $request)
    {
        $data = $request->validated();
        $this->cajaService->update_data($data);

        return response()->json([
            "success" => true,
            "message" => "Caja cerrada correctamente",
        ]);

    }

    //para el modal de historial de cajas
    public function show(string $id)
    {
        $datos = $this->cajaService->show_data($id);
        return response()->json([
            "success" => true,
            "datos" => $datos,
        ]);
    }

    public function anteriores()
    {
        return view("caja.anteriores.index", [
            "cajas" => Caja::all(),
        ]);
    }

    public function detalle(string $id)
    {
        $caja = Caja::with('user')->findOrFail($id);
        $transacciones = Venta::where("caja_id", $caja->id)->count();

        $mayorVentaRecord = Venta::where("caja_id", $caja->id)
            ->orderByDesc("total")
            ->first();
        $mayorVenta = $mayorVentaRecord ? $mayorVentaRecord->total : 0;

        $egresos = MovimientoCaja::where('tipo', 'egreso')
            ->where('caja_id', $caja->id)
            ->orderByDesc('monto')
            ->get();

        $totalEgreso = $egresos->sum('monto');

        $ingresos = MovimientoCaja::where('tipo', 'ingreso')
            ->where('caja_id', $caja->id)
            ->orderByDesc('monto')
            ->get();

        $totalIngreso = $ingresos->sum('monto');

        $promedioVenta = $transacciones > 0 ? $caja->monto_cierre / $transacciones : 0;

        $clientes = Venta::where("caja_id", $caja->id)
            ->get()
            ->unique("cliente_id")
            ->count();

        $metodos = Pago::where('caja_id', $caja->id)
            ->get()
            ->groupBy('metodo')
            ->map(function ($item) {
                return $item->sum('monto');
            });

        $efectivo = $metodos->get('efectivo', 0);
        $transferencia = $metodos->get('transferencia', 0);


        // Top 5 productos más vendidos
        $ventas = DetalleVenta::where("caja_id", $caja->id)
            ->with("producto:id,nombre")
            ->get()
            ->groupBy("producto_id")
            ->map(function ($items) {
                return [
                    "cantidad" => $items->sum("cantidad"),
                    "producto" => $items->first()->producto->nombre ?? 'Producto eliminado',
                    "total" => $items->sum("total"),
                ];
            })
            ->sortByDesc("total")
            ->take(5);

        $total = $efectivo + $transferencia;
        $efecPorcentaje = $total > 0 ? round((100 * $efectivo) / $total, 0) : 0;
        $transfPorcentaje = $total > 0 ? round((100 * $transferencia) / $total, 0) : 0;

        return view('caja.anteriores.detalle', [
            'caja' => $caja,
            'ventas' => $ventas->values(),
            'transacciones' => $transacciones,
            'clientes' => $clientes,
            'efectivo' => $efectivo,
            'efecPorcentaje' => $efecPorcentaje,
            'transferencia' => $transferencia,
            'transfPorcentaje' => $transfPorcentaje,
            'mayorVenta' => $mayorVenta,
            'promedio' => round($promedioVenta, 0),
            'egresos' => $egresos->take(5),
            'totalEgreso' => $totalEgreso,
            'ingresos' => $ingresos->take(5),
            'totalIngreso' => $totalIngreso,
        ]);
    }
}
