<?php

namespace App\Http\Controllers;

// use App\Events\AuditoriaCreadaEvent;
// use App\Events\UltimaActividadEvent;
use Carbon\Carbon;
use App\Actions\CreateVenta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreVentaRequest;
use App\Services\VentaService;
use Illuminate\Support\Facades\DB;
use App\Exports\VentasExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\UpdateVentaRequest;
use App\Models\{MovimientoCaja, User, Venta, DetalleVenta, Producto};
// use App\Jobs\GenerarPdfJob;
// use App\Jobs\VentaRealizada;

class VentaController extends Controller
{
    public function __construct(protected VentaService $ventaService)
    {
    }

    public function index_view()
    {
        $query = Venta::query();
        $users = User::whereIn('role', ['cliente', 'personal', 'mecanico'])
            ->where('tenant_id', tenant_id())
            ->get();
        $clientes = $users->count();

        $totalVentas = count($query->get());
        $ingresos = $query->sum('total');
        $ingresosHoy = $query->where('created_at', '>=', now()->format('Y-m-d'))->get()->sum('total');

        if (auth()->user()->role === 'admin') {
            $ventas = MovimientoCaja::orderByDesc('created_at')
                ->with('venta.productos')
                ->paginate(10);
        } else {
            $ventas = MovimientoCaja::orderByDesc('created_at')
                ->whereHas('venta', function ($query) {
                    $query->where('vendedor_id', auth()->user()->id);
                })
                ->with('venta.productos')
                ->paginate(10);
        }
        // dd($users);
        return view('caja.historial-completo.index', [
            'users' => $users,
            'clientes' => $clientes,
            'totalVentas' => $totalVentas,
            'ingresos' => $ingresos,
            'ingresosHoy' => $ingresosHoy,
            'ventas' => $ventas,
        ]);
    }

    public function index(Request $request)
    {
        try {
            $query = MovimientoCaja::query();
            $paginacion = $request->query('paginacion');
            $desdeC = $request->query('desde');
            $hastaC = $request->query('hasta');
            $estado = $request->query('estado');
            $formaPago = $request->query('formaPago');
            $tipo = $request->query('tipo');
            $search = $request->query('q');
            $orderBy = $request->query('orderBy');
            $dir = $request->query('direction');
            $cliente = $request->query('cliente');
            $caja = $request->query('caja');
            $mecanico = $request->query('mecanico');

            if ($paginacion === 'true' && !filled($desdeC) && !filled($hastaC) && !filled($formaPago) && !filled($tipo) && !filled($search) && !filled($orderBy) && !filled($cliente) && !filled($caja) && !filled($mecanico)) {
                if (auth()->user()->role === 'admin') {
                    $ventas = $query->with([
                        'caja.user',
                        'venta' => function ($query) {
                            $query->with(['cliente', 'detalleVentas', 'productos']);
                        }
                    ])->orderByDesc('created_at')->get()->take(10);
                } else {
                    $ventas = $query->whereHas('venta', function ($query) {
                        return $query->where('vendedor_id', auth()->user()->id);
                    })->with([
                                'caja.user',
                                'venta' => function ($query) {
                                    $query->with(['cliente', 'detalleVentas', 'productos']);
                                }
                            ])->orderByDesc('created_at')->get()->take(10);
                }

                return response()->json([
                    'success' => true,
                    'paginacion' => $paginacion,
                    'ventas' => $ventas,
                ]);
            }

            if (filled($mecanico)) {
                $query->whereHas('venta', function ($q) use ($mecanico) {
                    $q->whereHas('servicio', function ($query) use ($mecanico) {
                        $query->where('servicios_proceso.mecanico_id', $mecanico);
                    });
                });
            }

            if (filled($cliente)) {
                $query->whereHas('venta', function ($q) use ($cliente) {
                    $q->where('cliente_id', $cliente);
                });
            }
            if (filled($caja)) {
                $query->whereHas('caja', function ($q) use ($caja) {
                    $q->where('user_id', $caja);
                });
            }
            if (filled($desdeC)) {
                $desde = Carbon::parse($desdeC)->startOfDay();
                $query->where('created_at', '>=', $desde);
            }
            if (filled($hastaC)) {
                $hasta = Carbon::parse($hastaC)->endOfDay();
                $query->where('created_at', '<=', $hasta);
            }
            if (filled($estado)) {
                $query->whereHas('venta', function ($q) use ($estado) {
                    $q->where('estado', $estado);
                });
            }
            if (filled($formaPago)) {
                $query->whereHas('venta', function ($q) use ($formaPago) {
                    $q->where('forma_pago', $formaPago);
                });
            }
            if (filled($tipo)) {
                if ($tipo === 'venta') {
                    $query->where('venta_id', '!=', null);
                } elseif ($tipo === 'venta-ingreso') {
                    $query->where('tipo', 'ingreso');
                } elseif ($tipo === 'egreso') {
                    $query->where('tipo', 'egreso');
                } elseif ($tipo === 'ingreso') {
                    $query->where('tipo', 'ingreso')->where('concepto', '!=', 'Venta de productos');
                } elseif ($tipo === 'con_descuento') {
                    $query->whereHas('venta', function ($q) {
                        $q->where('con_descuento', true);
                    });
                } elseif ($tipo === 'sin_descuento') {
                    $query->where('tipo', 'ingreso')->whereHas('venta', function ($q) {
                        $q->where('con_descuento', false);
                    });
                }
            }

            if (filled($search)) {
                $query->whereHas('venta', function ($q) use ($search) {
                    $q->whereLike('codigo', "%$search%")
                        ->orWhereHas('productos', function ($q) use ($search) {
                            $q->whereLike('nombre', "%$search%");
                        })
                        ->orWhereHas('cliente', function ($q) use ($search) {
                            $q->whereLike('razon_social', "%$search%")
                                ->orWhereLike('ruc_ci', "%$search%");
                        });
                });
            }
            if (filled($orderBy) && filled($dir)) {
                $query->orderBy($orderBy, $dir);
            }

            if (auth()->user()->role === 'admin') {
                $ventas = $query->with([
                    'caja.user',
                    'venta' => function ($query) {
                        $query->with(['cliente', 'detalleVentas', 'productos']);
                    }
                ])->orderByDesc('created_at')->get();
            } else {
                $ventas = $query->whereHas('venta', function ($query) {
                    return $query->where('vendedor_id', auth()->user()->id);
                })->with([
                            'caja.user',
                            'venta' => function ($query) {
                                $query->with(['cliente', 'detalleVentas', 'productos']);
                            }
                        ])->orderByDesc('created_at')->get();
            }

            $egresosFiltros = $ventas->filter(fn($item) => $item->tipo === 'egreso')->sum('monto');
            $ingresosFiltros = $ventas->filter(fn($item) => $item->tipo === 'ingreso')->sum('monto');

            Cache::put('ventas', $ventas, 20);
            $clienteFilter = $ventas->filter(fn($item) => $item?->venta?->cliente->id == $cliente)->first();
            $clienteFilter = $clienteFilter?->venta?->cliente->razon_social;

            return response()->json([
                'success' => true,
                'ventas' => $ventas,
                'filtros' => [
                    'query' => $search,
                    'desde' => $desdeC,
                    'hasta' => $hastaC,
                    'estado' => $estado,
                    'formaPago' => $formaPago,
                    'tipo' => $tipo,
                    'search' => $search,
                    'orderBy' => $orderBy,
                    'dir' => $dir,
                    'cliente' => $clienteFilter,
                    'caja' => $caja,
                    'mecanico' => $mecanico,
                    'resultados' => $ventas->count(),
                ],
                'ingresos_filtro' => $ingresosFiltros,
                'egresos_filtro' => $egresosFiltros,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function show(string $codigo)
    {
        try {
            if (!is_numeric($codigo)) {
                $venta = Venta::where('codigo', $codigo)
                    ->with([
                        'detalleVentas',
                        'cliente',
                        'pagos',
                        'caja.user',
                        'vendedor',
                        'vehiculo',
                        'factura'
                    ])->first();

                $productos = Producto::whereHas('detalles', function ($query) use ($venta) {
                    return $query->where('venta_id', $venta->id);
                })->with([
                            'detalles' => function ($query) use ($venta) {
                                $query->where('venta_id', $venta->id);
                            }
                        ])->get();
            }
            if (is_numeric($codigo)) {
                $venta = MovimientoCaja::with([
                    'caja.user',
                    'venta.vendedor',
                ])->find($codigo);

            }

            return response()->json([
                'success' => true,
                'productos' => $productos ?? '',
                'venta' => $venta,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function store(StoreVentaRequest $request, CreateVenta $createVenta): JsonResponse
    {
        $data = $request->validated();
        $res = $createVenta->execute($data);
        return response()->json([
            'success' => true,
            'message' => 'Venta creada correctamente',
            'venta' => $res['venta'],
            'productos' => $res['productos'],
        ], 200);
    }

    public function update(UpdateVentaRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $venta = Venta::findOrFail($id);
            if ($venta->estado == 'cancelado') {
                return response()->json([
                    'message' => 'venta anulada'
                ]);
            }
            $venta->update($data);
            $cajaId = $venta->caja_id;
            $stock = 0;
            $ventas = 0;
            $detalleVenta = DetalleVenta::where('venta_id', $venta->id)
                ->with('producto')
                ->get();

            foreach ($detalleVenta as $detalle) {
                $producto = $detalle->producto;
                if ($producto->tipo == 'producto') {
                    $stock = $producto->stock + $detalle->cantidad;
                    $ventas = $producto->ventas - $detalle->cantidad;
                    $producto->update([
                        'ventas' => $ventas,
                        'stock' => $stock,
                    ]);
                }
            }

            MovimientoCaja::create([
                'caja_id' => $cajaId,
                'tipo' => 'egreso',
                'venta_anulado' => $venta->id,
                'concepto' => "Anulación de venta: #$venta->codigo",
                'monto' => $venta->total,
            ]);


            DB::commit();
            return response()->json([
                'message' => 'Venta Actualizado'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }



    public function export_excel()
    {
        return Excel::download(new VentasExport, 'ventas.xlsx');
    }

    public function export_pdf()
    {

        $item = Cache::get('ventas');

        if (filled($item)) {
            $mov = $item->contains(fn($value) => $value->venta == null);
        }
        if (!filled($item) || $mov) {
            $item = Cache::remember('ventas', 20, fn() => MovimientoCaja::with('caja.user:id,name')->get());
            Cache::forget('ventas');
            // $ingresos = $item->sum('monto');
            // $egresos = $item->where('tipo', 'egreso')->sum('monto');
            $ventas = $item->toArray();
            // $items = count($ventas);

            // Auditoria::create([
            //     'created_by' => auth()->user()->id,
            //     'entidad_type' => User::class,
            //     'entidad_id' => auth()->user()->id,
            //     'accion' => 'Reporte Generado',
            // ]);

            // GenerarPdfJob::dispatch(auth()->user()->id, $ventas, $ingresos, $egresos, tenant_id());
            return response()->json([
                'data' => 'listo',
            ]);
        } else {
            $ventas = $item->toArray();
            // $items = count($ventas);
            Cache::forget('ventas');
            // Auditoria::create([
            //     'created_by' => auth()->user()->id,
            //     'entidad_type' => User::class,
            //     'entidad_id' => auth()->user()->id,
            //     'accion' => 'Reporte Generado',
            // ]);
            // GenerarPdfJob::dispatch(auth()->user()->id, $ventas, null, null, tenant_id());
            return response()->json([
                'data' => 'listo',
            ]);
        }
    }
}
