<?php

namespace App\Enums;

enum TipoMovimiento: string
{
    case INGRESO = 'ingreso';
    case EGRESO = 'egreso';
}
