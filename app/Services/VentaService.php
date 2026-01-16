<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Producto;
use App\Models\Caja;
use App\Models\Factura;
use Illuminate\Support\Facades\Log;

class VentaService
{
    public function validar_carrito($data)
    {
        $carrito = collect(json_decode($data['carrito']));
        $ruc = $data['ruc'];
        $errores = [];
        session(['ruc' => $ruc]);
        foreach ($carrito as $id => $producto) {
            if (!Producto::find($id)) {
                $errores['productos'] = ["producto con id: $id, no existe"];
            }
        }
        if (!User::where('ruc_ci', $ruc)->first()) {
            $errores['user'] = ['El usuario no existe'];
        }

        if (collect($errores)->count() > 0) {
            throw new \Exception(json_encode($errores));
        }
    }

    public function data_venta(array $data): array
    {
        try {
            $carrito = collect(json_decode($data['carrito']));
            $totalCarrito = collect(json_decode($data['total']));
            $formaPago = collect(json_decode($data['forma_pago']));
            $vehiculoId = $data['vehiculo_id'] ?? null;
            $montoRecibido = $data['monto_recibido'];
            $servicioId = $data['servicio_id'] ?? null;

            $ruc = $data['ruc'];
            $userId = User::where('ruc_ci', $ruc)
                ->where('tenant_id', tenant_id())
                ->pluck('id')
                ->first();
            $cajaId = Caja::where('estado', 'abierto')->pluck('id')->first();
            $metodoPago = $formaPago->keys();
            session(['key' => $metodoPago[0]]);
            $tieneDescuento = $carrito->contains(fn($item) => $item->descuento === true);
            return [
                'carrito' => $carrito,
                'totalCarrito' => $totalCarrito,
                'formaPago' => $formaPago,
                'vehiculoId' => $vehiculoId,
                'userId' => $userId,
                'cajaId' => $cajaId,
                'metodoPago' => $metodoPago,
                'tieneDescuento' => $tieneDescuento,
                'montoRecibido' => $montoRecibido,
                'servicioId' => $servicioId,
            ];
        } catch (\Exception $e) {
            Log::error('69: App\Services\VentaService | Error al obtener los datos de la venta: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
    public function crear_factura(object $venta): void
    {
        try {
            $cliente = User::find($venta->cliente_id);
            $factura = Factura::orderBy('numero', 'desc')->first();
            if ($cliente->ruc_ci != '1111111-1') {
                $numeroFacturaInicial = session('numero_factura_inicial');
                if ($numeroFacturaInicial !== null) {
                    $nuevoNumero = $numeroFacturaInicial;
                    session()->forget('numero_factura_inicial');
                } else {
                    $nuevoNumero = $factura?->numero !== null ? $factura->numero + 1 : 87;
                }
                $timbradoSession = session('timbrado_factura');
                $timbrado = $timbradoSession !== null ? $timbradoSession : 18450157;

                $factura = Factura::create([
                    'venta_id' => $venta->id,
                    'timbrado' => $timbrado,
                    'sucursal' => 001,
                    'punto_emision' => 001,
                    'numero' => $nuevoNumero,
                    'emision' => now()->format('Y-m-d'),
                    'estado' => 'emitida',
                    'tipo' => 'factura',
                    'condicion_venta' => 'contado',
                ]);
            }
            // Log::info('Factura creada: ' . $factura);
        } catch (\Exception $e) {
            Log::error('Error al crear la factura: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
