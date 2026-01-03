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

    public function detalle_show_data(string $id)
    {
        try {
            $caja = Caja::with('user')->findOrFail($id);

            $stats = Venta::where('caja_id', $caja->id)
                ->selectRaw(
                    'COUNT(DISTINCT cliente_id) as clientes,
                COUNT(*) as transacciones,
                MAX(total) as mayor_venta'
                )->first();

            $clientes = $stats->clientes;
            $transacciones = $stats->transacciones;
            $mayorVenta = $stats->mayor_venta;

            $totales = MovimientoCaja::where('caja_id', $caja->id)
                ->selectRaw("
                SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) as total_ingreso,
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as total_egreso
            ")
                ->first();

            $totalIngreso = $totales->total_ingreso;
            $totalEgreso = $totales->total_egreso;


            $ingresos = MovimientoCaja::where('caja_id', $caja->id)
                ->where('tipo', 'ingreso')
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            $egresos = MovimientoCaja::where('caja_id', $caja->id)
                ->where('tipo', 'egreso')
                ->orderByDesc('id')
                ->limit(5)
                ->get();

            $promedioVenta = $transacciones > 0 ? $caja->monto_cierre / $transacciones : 0;

            $metodosPago = Pago::where('caja_id', $caja->id)
                ->get()
                ->groupBy('metodo')
                ->map(function ($item) {
                    return $item->sum('monto');
                });

            $efectivo = $metodosPago->get('efectivo', 0);
            $transferencia = $metodosPago->get('transferencia', 0);

            $ventas = DetalleVenta::where("caja_id", $caja->id)
                ->with("producto:id,nombre")
                ->limit(5)
                ->get()
                ->groupBy("producto_id")
                ->map(function ($items) {
                    return [
                        "cantidad" => $items->sum("cantidad"),
                        "producto" => $items->first()->producto->nombre ?? 'Producto eliminado',
                        "total" => $items->sum("total"),
                    ];
                })
                ->sortByDesc("total");

            $total = $efectivo + $transferencia;
            $efecPorcentaje = $total > 0 ? round((100 * $efectivo) / $total, 0) : 0;
            $transfPorcentaje = $total > 0 ? round((100 * $transferencia) / $total, 0) : 0;

            return [
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
                'egresos' => $egresos,
                'totalEgreso' => $totalEgreso,
                'ingresos' => $ingresos,
                'totalIngreso' => $totalIngreso,
            ];

        } catch (\Exception $e) {
            throw new CajaIndexException($e->getMessage());
        }
    }
}