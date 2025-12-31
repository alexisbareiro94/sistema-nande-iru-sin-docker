<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class VentaException extends Exception
{
    /**
     * Reportar el error (opcional)
     */
    public function report()
    {
        Log::error("Fallo crítico en el proceso de venta: " . $this->getMessage());
    }

    /**
     * Cómo se verá el error en el navegador/API (opcional)
     */
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'error' => 'No se pudo completar la venta',
            'detalle' => $this->getMessage()
        ], 500);
    }
}