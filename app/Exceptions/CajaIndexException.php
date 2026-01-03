<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;
use Exception;

class CajaIndexException extends Exception
{
    public function report()
    {
        Log::error("Fallo crítico en el proceso de caja: " . $this->getMessage());
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Error al abrir la caja',
            'detalle' => $this->getMessage()
        ], 500);
    }
}
