<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\Producto;
use Illuminate\Support\Facades\Log;

class DetalleVentaService
{
    public function registrar(object $carrito, int $ventaId, int $cajaId): array
    {
        try {
            $productos = [];
            foreach ($carrito as $id => $producto) {

                DetalleVenta::create([
                    'venta_id' => $ventaId,
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
                        Log::error('45: App\Services\DetalleVentaService: No hay stock suficiente: ' . $productdb->nombre);
                        throw new \Exception('No hay stock suficiente: ' . $productdb->nombre);
                    }
                }
                $productdb->increment('ventas', $producto->cantidad);
            }
            return $productos;
        } catch (\Exception $e) {
            Log::error('44: App\Services\DetalleVentaService: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}



//  $productos = [];
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