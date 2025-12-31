<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Venta;
use App\Services\PagoService;
use App\Services\VentaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Actions\ServicioCobrado;
use App\Services\MovimientoService;
use App\Enums\{ConceptoMovimiento, TipoMovimiento};
use App\Services\DetalleVentaService;
use App\Exceptions\VentaException;

class CreateVenta
{
    public function __construct(
        public VentaService $ventaService,
        public ServicioCobrado $servicioCobrado,
        public MovimientoService $movimientoService,
        public DetalleVentaService $detalleVentaService,
        public PagoService $pagoService,
    ) {
    }
    public function execute($data): array
    {
        $this->ventaService->validar_carrito($data); //aca valido los datos del carrito y el usuario        
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
            $this->ventaService->crear_factura($venta);
            $this->servicioCobrado->execute($validated['vehiculoId'] ?? null, $venta->id);
            $this->movimientoService->registrar($validated['cajaId'], $venta->total, ConceptoMovimiento::VENTA->value, TipoMovimiento::INGRESO->value);
            $productos = $this->detalleVentaService->registrar($validated['carrito'], $venta->id, $validated['cajaId']);
            $this->pagoService->registrar($validated['formaPago'], $venta, $validated['cajaId']);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Venta realizada con exito',
                'venta' => $venta->load('cliente:id,razon_social,ruc_ci'),
                'productos' => $productos,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear la venta: ' . $e->getMessage());
            throw new VentaException("Error al procesar la transacción de venta", 0, $e);
        }
    }
}
