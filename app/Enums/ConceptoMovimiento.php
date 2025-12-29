<?php

namespace App\Enums;

enum ConceptoMovimiento: string
{
    case APERTURA = 'Apertura de caja';
    case CIERRE = 'Cierre de caja';
    case PAGO_SALRIO = 'Pago de salario';
    case VENTA = 'Venta de productos';
}
