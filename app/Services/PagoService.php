<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Pago;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
class PagoService
{
    public function registrar(Collection $formaPago, object $venta, int $cajaId): void
    {
        try {
            foreach ($formaPago as $forma => $datos) {
                if ($forma == 'mixto') {
                    foreach ($datos as $metodo => $monto) {
                        Pago::create([
                            'venta_id' => $venta->id,
                            'caja_id' => $cajaId,
                            'metodo' => $metodo,
                            'monto' => $monto,
                            'estado' => 'completado',
                        ]);
                    }
                } else {
                    Pago::create([
                        'venta_id' => $venta->id,
                        'caja_id' => $cajaId,
                        'metodo' => $forma,
                        'monto' => $datos->total,
                        'estado' => 'completado',
                    ]);
                }
            }

            $caja = session('caja');
            $caja['saldo'] += $venta->total;
            session()->put(['caja' => $caja]);
            crear_caja();
        } catch (\Exception $e) {
            Log::error('44: App\Services\PagoService Error al registrar el pago: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
