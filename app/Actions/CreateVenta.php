<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Actions\ServicioCobrado;
use App\Services\MovimientoService;
use App\Enums\{ConceptoMovimiento, TipoMovimiento};

class CreateVenta
{
    public function __construct(
        public VentaService $ventaService,
        public ServicioCobrado $servicioCobrado,
        public MovimientoService $movimientoService
    ) {
    }
    public function execute($data)
    {
        $errores = $this->ventaService->validar_carrito($data); //aca valido los datos del carrito y el usuario        

        if (collect($errores)->count() > 0) {
            return response()->json([
                'success' => false,
                'errores' => $errores,
            ], 400);
        }

        $validated = $this->ventaService->data_venta($data);
        DB::beginTransaction();
        try {
            $venta = Venta::create([
                'caja_id' => $validated['cajaId'],
                'codigo' => generate_code(),
                'vendedor_id' => auth()->user()->id,
                'cliente_id' => $validated['userId'],
                'vehiculo_id' => $validated['vehiculoId'] ?? null,
                'cantidad_productos' => $validated['totalCarrito']['cantidadTotal'],
                'forma_pago' => $validated['metodoPago'][0],
                'con_descuento' => $validated['tieneDescuento'],
                'monto_descuento' => $validated['totalCarrito']['subtotal'] - $validated['totalCarrito']['total'],
                'monto_recibido' => $validated['montoRecibido'],
                'subtotal' => $validated['totalCarrito']['subtotal'],
                'total' => $validated['totalCarrito']['total'],
                'estado' => 'completado',
            ]);
            Log::debug('Venta creada: ' . $venta->id);
            $this->ventaService->crear_factura($venta);
            $this->servicioCobrado->execute($data['vehiculoId'], $venta->id);
            $this->movimientoService->registrar($validated['cajaId'], $validated['montoRecibido'], ConceptoMovimiento::VENTA, TipoMovimiento::INGRESO);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear la venta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }



    }

    // try {
    //         $venta = Venta::create([
    //             'caja_id' => $cajaId,
    //             'codigo' => generate_code(),
    //             'vendedor_id' => auth()->user()->id,
    //             'cliente_id' => $userId,
    //             'vehiculo_id' => $vehiculoId ?? null,
    //             'cantidad_productos' => $totalCarrito['cantidadTotal'],
    //             'forma_pago' => $metodoPago[0],
    //             'con_descuento' => $tieneDescuento,
    //             'monto_descuento' => $totalCarrito['subtotal'] - $totalCarrito['total'],
    //             'monto_recibido' => $data['monto_recibido'],
    //             'subtotal' => $totalCarrito['subtotal'],
    //             'total' => $totalCarrito['total'],
    //             'estado' => 'completado',
    //         ]);
    //         $cliente = User::find($userId);
    //         $factura = Factura::orderBy('numero', 'desc')->first();
    //         if ($cliente->ruc_ci != '1111111-1') {
    //             // Verificar si existe número configurado en session
    //             $numeroFacturaInicial = session('numero_factura_inicial');
    //             if ($numeroFacturaInicial !== null) {
    //                 $nuevoNumero = $numeroFacturaInicial;
    //                 // Eliminar la session después de usarla
    //                 session()->forget('numero_factura_inicial');
    //             } else {
    //                 $nuevoNumero = $factura?->numero !== null ? $factura->numero + 1 : 87;
    //             }

    //             // Verificar si existe timbrado configurado en session
    //             $timbradoSession = session('timbrado_factura');
    //             $timbrado = $timbradoSession !== null ? $timbradoSession : 18450157;

    //             Factura::create([
    //                 'venta_id' => $venta->id,
    //                 'timbrado' => $timbrado,
    //                 'sucursal' => 001,
    //                 'punto_emision' => 001,
    //                 'numero' => $nuevoNumero,
    //                 'emision' => now()->format('Y-m-d'),
    //                 'estado' => 'emitida',
    //                 'tipo' => 'factura',
    //                 'condicion_venta' => 'contado',
    //             ]);
    //         }


    //         if ($vehiculoId != null) {
    //             ServicioProceso::where('vehiculo_id', $vehiculoId)
    //                 ->update([
    //                     'estado' => 'cobrado',
    //                     'venta_id' => $venta->id,
    //                     'updated_by' => auth()->user()->id,
    //                     'fecha_fin' => now(),
    //                 ]);
    //         }

    //         // Auditoria::create([
    //         //     'created_by' => auth()->user()->id,
    //         //     'entidad_type' => Venta::class,
    //         //     'entidad_id' => $venta->id,
    //         //     'accion' => 'Registro de venta',
    //         //     'datos' => [
    //         //         'total' => $venta->total,
    //         //     ]
    //         // ]);

    //         // AuditoriaCreadaEvent::dispatch(tenant_id());

    //         MovimientoCaja::create([
    //             'caja_id' => $cajaId,
    //             'tipo' => 'ingreso',
    //             'venta_id' => $venta->id,
    //             'concepto' => 'Venta de productos',
    //             'monto' => $venta->total,
    //         ]);

    //         $productos = [];
    //         foreach ($carrito as $id => $producto) {
    //             DetalleVenta::create([
    //                 'venta_id' => $venta->id,
    //                 'producto_id' => $id,
    //                 'caja_id' => $cajaId,
    //                 'cantidad' => $producto->cantidad,
    //                 'precio_unitario' => $producto->precio,
    //                 'producto_con_descuento' => $producto->descuento,
    //                 'precio_descuento' => $producto->precio_descuento,
    //                 'subtotal' => $producto->cantidad * $producto->precio,
    //                 'total' => $producto->descuento === true ? $producto->cantidad * $producto->precio_descuento : $producto->cantidad * $producto->precio,
    //             ]);

    //             $productdb = Producto::find($id);
    //             $productos[] = $productdb;
    //             if ($productdb->tipo === 'producto') {
    //                 if ($producto->cantidad <= $productdb->stock) {
    //                     $productdb->decrement('stock', $producto->cantidad);
    //                 } else {
    //                     DB::rollBack();
    //                     return response()->json([
    //                         'success' => false,
    //                         'error' => 'No hay stock suficiente: ' . $producto->nombre,
    //                         'stock' => $productdb->stock,
    //                         'carrito_cantidad' => $producto->cantidad,
    //                     ], 400);
    //                 }
    //             }
    //             $productdb->increment('ventas', $producto->cantidad);
    //         }

    //         foreach ($formaPago as $forma => $monto) {
    //             if ($forma == 'mixto') {
    //                 foreach ($monto as $metodo => $pago) {
    //                     Pago::create([
    //                         'venta_id' => $venta->id,
    //                         'caja_id' => $cajaId,
    //                         'metodo' => $metodo,
    //                         'monto' => $pago,
    //                         'estado' => 'completado',
    //                     ]);
    //                 }
    //             } else {
    //                 Pago::create([
    //                     'venta_id' => $venta->id,
    //                     'caja_id' => $cajaId,
    //                     'metodo' => $forma,
    //                     'monto' => $monto,
    //                     'estado' => 'completado',
    //                 ]);
    //             }
    //         }

    //         $caja = session('caja');
    //         $caja['saldo'] += $venta->total;
    //         session()->put(['caja' => $caja]);
    //         DB::commit();
    //         // VentaRealizada::dispatch($venta, tenant_id());
    //         // UltimaActividadEvent::dispatch(auth()->user()->id, $venta->total, tenant_id());
    //         crear_caja();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Venta realizada con exito',
    //             'venta' => $venta->load('cliente:id,razon_social,ruc_ci'),
    //             'productos' => $productos,
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'error' => $e->getMessage(),
    //         ], 400);
    //     }
}
