<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\ServicioProceso;

class ServicioCobrado
{
    public function execute(?int $vehiculoId, int $ventaId): void
    {
        if ($vehiculoId === null) {
            return;
        }
        ServicioProceso::where('vehiculo_id', $vehiculoId)
            ->update([
                'estado' => 'cobrado',
                'venta_id' => $ventaId,
                'updated_by' => auth()->user()->id,
                'fecha_fin' => now(),
            ]);
    }
}
