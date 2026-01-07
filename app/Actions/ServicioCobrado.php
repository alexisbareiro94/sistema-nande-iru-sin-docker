<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\ServicioProceso;

class ServicioCobrado
{
    public function execute(int $ventaId, ?int $servicioId): void
    {
        if ($servicioId === null) {
            return;
        }
        ServicioProceso::findOrFail($servicioId)
            ->update([
                'estado' => 'cobrado',
                'venta_id' => $ventaId,
                'updated_by' => auth()->user()->id,
                'fecha_fin' => now(),
            ]);
    }
}
