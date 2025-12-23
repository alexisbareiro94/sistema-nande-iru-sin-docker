<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $table = "facturas";
    protected $fillable = [
        'venta_id',
        'timbrado',
        'sucursal',
        'punto_emision',
        'numero',
        'emision',
        'estado',
        'tipo',
        'condicion_venta',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
