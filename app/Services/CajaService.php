<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CajaIndexException;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Caja;
use App\Models\PagoSalario;
use App\Models\MovimientoCaja;
use App\Models\Venta;
use App\Models\Pago;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\Validator;

class CajaService
{
    public function set_data(array $res): array
    {
        return [
            'user_id' => Auth::id(),
            'monto_inicial' => $res['monto_inicial'],
            'monto_cierre' => null,
            'estado' => 'abierto',
            'diferencia' => null,
            'fecha_apertura' => now(),
            'fecha_cierre' => null,
            'observaciones' => null,
            'saldo_esperado' => null,
        ];
    }

    public function index_view_data(): array
    {
        if (!session("caja")) {
            $caja = Caja::orderByDesc("id")->first();
        }
        try {
            $tenantId = tenant_id();
            $pagosSalario = PagoSalario::whereHas('user', function ($q) use ($tenantId) {
                return $q->where('role', 'personal')
                    ->where('activo', true)
                    ->where('tenant_id', $tenantId);
            })
                ->orderByDesc('created_at')
                ->with([
                    'user' => function ($q) use ($tenantId) {
                        $q->where('tenant_id', $tenantId);
                    }
                ])

                ->get()
                ->unique('user_id');

            $users = User::where('role', 'personal')
                ->where('activo', true)
                ->where('tenant_id', $tenantId)
                ->get();

            return [
                'users' => $users,
                'pagosSalario' => $pagosSalario,
                'caja' => $caja ?? null,
            ];
        } catch (\Exception $e) {
            throw new CajaIndexException($e->getMessage());
        }
    }

    public function abrir_data(array $res): void
    {
        try {
            if (session("caja")) {
                throw new CajaIndexException("Ya existe una caja abierta");
            }

            $tenantId = tenant_id();
            session("caja", []);
            $caja = Caja::create($this->set_data($res));

            MovimientoCaja::create([
                "caja_id" => $caja->id,
                "tipo" => "ingreso",
                "concepto" => "Apertura de caja",
                "monto" => $caja["monto_inicial"],
            ]);

            $arrayCaja = $caja->load([
                'user' => function ($q) use ($tenantId) {
                    $q->select('id', 'name')
                        ->where('tenant_id', $tenantId);
                }
            ])->toArray();

            $arrayCaja["saldo"] = $arrayCaja["monto_inicial"];
            session()->put(["caja" => $arrayCaja]);
        } catch (\Exception $e) {
            throw new CajaIndexException($e->getMessage());
        }
    }

    public function update_data(array $data): void
    {
        try {
            $caja = Caja::where("estado", "abierto")->first();
            if ($caja == null) {
                throw new CajaIndexException("La caja ya esta cerrada");
            }
            $caja->update([
                "monto_cierre" => $data["monto_cierre"],
                "saldo_esperado" => $data["saldo_esperado"],
                "diferencia" => $data["diferencia"],
                "observaciones" => $data["observaciones"],
                "egresos" => $data["egreso"],
                "fecha_cierre" => now(),
                "estado" => "cerrado",
                "updated_by" => auth()->user()->id,
            ]);

            session()->forget("caja");

        } catch (\Exception $e) {
            throw new CajaIndexException($e->getMessage());
        }
    }

    public function show_data(string $id): array
    {
        try {
            $caja = Caja::with('user')->findOrFail($id);
            $transacciones = Venta::where("caja_id", $caja->id)->count();
            $mayorVenta = Venta::where("caja_id", $caja->id)
                ->orderByDesc("total")
                ->first()?->total;

            $egresos = MovimientoCaja::where('tipo', 'egreso')
                ->where('caja_id', $caja->id)
                ->orderByDesc('monto')
                ->get();

            $totalEgreso = $egresos->sum('monto');

            $promedioVenta = $caja?->monto_cierre ? 0 : $caja->monto_cierre / $transacciones;
            $clientes = Venta::where("caja_id", $caja->id)
                ->get()
                ->unique("cliente_id")
                ->count();

            //TODO: usar algun map y un groupBy, para la columna metodo
            $efectivo = Pago::where("caja_id", $caja->id)
                ->where("metodo", "efectivo")
                ->sum("monto");
            $transferencia = Pago::where("caja_id", $caja->id)
                ->where("metodo", "transferencia")
                ->sum("monto");

            $ventas = DetalleVenta::where("caja_id", $caja->id)
                ->with("producto:id,nombre")
                ->get()
                ->groupBy("producto_id")
                ->map(function ($items) {
                    return [
                        "cantidad" => $items->sum("cantidad"),
                        "producto" => $items->first()->producto->nombre,
                        "total" => $items->sum("total"),
                    ];
                })
                ->sortByDesc("total")
                ->take(3);

            $total = $efectivo + $transferencia;
            $efecPorcentaje = $total ? (100 * $efectivo) / $total : 0;
            $transfProcentaje = $total ? (100 * $transferencia) / $total : 0;


            return [
                "caja" => $caja,
                "ventas" => $ventas->values()->toArray(),
                "transacciones" => $transacciones,
                "clientes" => $clientes,
                "efectivo" => $efectivo,
                "efecPorcentaje" => round($efecPorcentaje, 0),
                "transferencia" => $transferencia,
                "transfProcentaje" => round($transfProcentaje, 0),
                "mayorVenta" => $mayorVenta,
                "promedio" => round($promedioVenta, 0),
                'egresos' => $egresos->take(3),
                'total_egreso' => $totalEgreso
            ];
        } catch (\Exception $e) {
            throw new CajaIndexException($e->getMessage());
        }
    }
}