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
        $datos = $this->cajaService->detalle_show_data($id);
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
        $data = $this->cajaService->detalle_show_data($id);
        return view("caja.anteriores.detalle", $data);
    }
}
