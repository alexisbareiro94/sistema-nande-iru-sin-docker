<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Producto;
use App\Models\Caja;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class VentaService
{
    public function validar_carrito($data): Collection
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

        return collect($errores);

    }

    public function data_venta(array $data): array
    {
        $carrito = collect(json_decode($data['carrito']));
        $totalCarrito = collect(json_decode($data['total']));
        $formaPago = collect(json_decode($data['forma_pago']));
        $vehiculoId = $data['vehiculo_id'] ?? null;
        $montoRecibido = $data['monto_recibido'];

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
        ];

    }
}
