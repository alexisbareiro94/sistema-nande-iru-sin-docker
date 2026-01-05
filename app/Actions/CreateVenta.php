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
                'caja_id' => (int) $validated['cajaId'],
                'codigo' => generate_code(),
                'vendedor_id' => (int) auth()->user()->id,
                'cliente_id' => (int) $validated['userId'],
                'vehiculo_id' => $validated['vehiculoId'] ?? null,
                'cantidad_productos' => (int) $validated['totalCarrito']['cantidadTotal'],
                'forma_pago' => $validated['metodoPago'][0],
                'con_descuento' => $validated['tieneDescuento'],
                'monto_descuento' => (int) $validated['totalCarrito']['subtotal'] - (int) $validated['totalCarrito']['total'],
                'monto_recibido' => (int) $validated['montoRecibido'],
                'subtotal' => (int) $validated['totalCarrito']['subtotal'],
                'total' => (int) $validated['totalCarrito']['total'],
                'estado' => 'completado',
            ]);
            $this->ventaService->crear_factura($venta);
            $this->servicioCobrado->execute((int) $validated['vehiculoId'] ?? null, $venta->id);
            $this->movimientoService->registrar($validated['cajaId'], $venta, ConceptoMovimiento::VENTA->value, TipoMovimiento::INGRESO->value);
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
