<?php

namespace App\Http\Controllers;

use App\Events\AuditoriaCreadaEvent;
use App\Events\UltimaActividadEvent;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVentaRequest;
use App\Services\VentaService;
use App\Models\{Auditoria, MovimientoCaja, User, Venta, DetalleVenta, Caja, Pago, Producto, ServicioProceso};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\VentasExport;
use App\Http\Requests\UpdateVentaRequest;
use App\Jobs\GenerarPdfJob;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use App\Jobs\VentaRealizada;
use function Symfony\Component\Clock\now;

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
            //TODO: agregar el fulltext
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

            return response()->json([
                'success' => true,
                'ventas' => $ventas,
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
                        'vehiculo'
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
                $venta = MovimientoCaja::find($codigo)->load(['caja.user', 'venta.vendedor']);
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

    public function store(StoreVentaRequest $request)
    {
        $data = $request->validated();  //aca se valida que llegue el carrito y demas datos        
        $errores = $this->ventaService->validate_data($data); //aca valido los datos del carrito y el usuario        
        if ($errores->count() > 0) {
            return response()->json([
                'success' => false,
                'errores' => $errores->first(),
                'es en el service'
            ], 400);
        }

        $carrito = collect(json_decode($data['carrito']));
        $totalCarrito = collect(json_decode($data['total']));
        $formaPago = collect(json_decode($data['forma_pago']));
        $vehiculoId = $data['vehiculo_id'] ?? null;

        // return response()->json([
        //     'success' => true,
        //     'vehiculo_id' => $vehiculoId,
        // ]);

        $ruc = $data['ruc'];
        $userId = User::where('ruc_ci', $ruc)
            ->where('tenant_id', tenant_id())
            ->pluck('id')
            ->first();
        $cajaId = Caja::where('estado', 'abierto')->pluck('id')->first();
        $metodoPago = $formaPago->keys();
        session(['key' => $metodoPago[0]]);
        $tieneDescuento = $carrito->contains(fn($item) => $item->descuento === true);

        DB::beginTransaction();
        try {
            $venta = Venta::create([
                'caja_id' => $cajaId,
                'codigo' => generate_code(),
                'vendedor_id' => auth()->user()->id,
                'cliente_id' => $userId,
                'vehiculo_id' => $vehiculoId ?? null,
                'cantidad_productos' => $totalCarrito['cantidadTotal'],
                'forma_pago' => $metodoPago[0],
                'con_descuento' => $tieneDescuento,
                'monto_descuento' => $totalCarrito['subtotal'] - $totalCarrito['total'],
                'monto_recibido' => $data['monto_recibido'],
                'subtotal' => $totalCarrito['subtotal'],
                'total' => $totalCarrito['total'],
                'estado' => 'completado',
            ]);

            if ($vehiculoId != null) {
                ServicioProceso::where('vehiculo_id', $vehiculoId)
                    ->update([
                        'estado' => 'cobrado',
                        'venta_id' => $venta->id,
                        'updated_by' => auth()->user()->id,
                        'fecha_fin' => now(),
                    ]);
            }

            Auditoria::create([
                'created_by' => auth()->user()->id,
                'entidad_type' => Venta::class,
                'entidad_id' => $venta->id,
                'accion' => 'Registro de venta',
                'datos' => [
                    'total' => $venta->total,
                ]
            ]);

            AuditoriaCreadaEvent::dispatch(tenant_id());

            MovimientoCaja::create([
                'caja_id' => $cajaId,
                'tipo' => 'ingreso',
                'venta_id' => $venta->id,
                'concepto' => 'Venta de productos',
                'monto' => $venta->total,
            ]);

            $productos = [];
            foreach ($carrito as $id => $producto) {
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $id,
                    'caja_id' => $cajaId,
                    'cantidad' => $producto->cantidad,
                    'precio_unitario' => $producto->precio,
                    'producto_con_descuento' => $producto->descuento,
                    'precio_descuento' => $producto->precio_descuento,
                    'subtotal' => $producto->cantidad * $producto->precio,
                    'total' => $producto->descuento === true ? $producto->cantidad * $producto->precio_descuento : $producto->cantidad * $producto->precio,
                ]);

                $productdb = Producto::find($id);
                $productos[] = $productdb;
                if ($productdb->tipo === 'producto') {
                    if ($producto->cantidad <= $productdb->stock) {
                        $productdb->decrement('stock', $producto->cantidad);
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'error' => 'No hay stock suficiente: ' . $producto->nombre,
                            'stock' => $productdb->stock,
                            'carrito_cantidad' => $producto->cantidad,
                        ], 400);
                    }
                }
                $productdb->increment('ventas', $producto->cantidad);
            }

            foreach ($formaPago as $forma => $monto) {
                if ($forma == 'mixto') {
                    foreach ($monto as $metodo => $pago) {
                        Pago::create([
                            'venta_id' => $venta->id,
                            'caja_id' => $cajaId,
                            'metodo' => $metodo,
                            'monto' => $pago,
                            'estado' => 'completado',
                        ]);
                    }
                } else {
                    Pago::create([
                        'venta_id' => $venta->id,
                        'caja_id' => $cajaId,
                        'metodo' => $forma,
                        'monto' => $monto,
                        'estado' => 'completado',
                    ]);
                }
            }

            $caja = session('caja');
            $caja['saldo'] += $venta->total;
            session()->put(['caja' => $caja]);
            DB::commit();
            VentaRealizada::dispatch($venta, tenant_id());
            UltimaActividadEvent::dispatch(auth()->user()->id, $venta->total, tenant_id());
            crear_caja();
            return response()->json([
                'success' => true,
                'message' => 'Venta realizada con exito',
                'venta' => $venta->load('cliente:id,razon_social,ruc_ci'),
                'productos' => $productos,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
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
            Auditoria::create([
                'created_by' => auth()->user()->id,
                'entidad_type' => Venta::class,
                'entidad_id' => $venta->id,
                'accion' => "Anulación de venta: #$venta->codigo",
                'datos' => [
                    'total' => $venta->total,
                ]
            ]);

            AuditoriaCreadaEvent::dispatch(tenant_id());

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
            $ingresos = $item->sum('monto');
            $egresos = $item->where('tipo', 'egreso')->sum('monto');
            $ventas = $item->toArray();
            $items = count($ventas);

            Auditoria::create([
                'created_by' => auth()->user()->id,
                'entidad_type' => User::class,
                'entidad_id' => auth()->user()->id,
                'accion' => 'Reporte Generado',
            ]);

            GenerarPdfJob::dispatch(auth()->user()->id, $ventas, $ingresos, $egresos, tenant_id());
            return response()->json([
                'data' => 'listo',
            ]);
        } else {
            $ventas = $item->toArray();
            // $items = count($ventas);
            Cache::forget('ventas');
            Auditoria::create([
                'created_by' => auth()->user()->id,
                'entidad_type' => User::class,
                'entidad_id' => auth()->user()->id,
                'accion' => 'Reporte Generado',
            ]);
            GenerarPdfJob::dispatch(auth()->user()->id, $ventas, null, null, tenant_id());
            return response()->json([
                'data' => 'listo',
            ]);
        }
    }
}
