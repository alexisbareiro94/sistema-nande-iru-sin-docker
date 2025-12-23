<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
class FacturaFoto extends Model
{
    use Auditable;
    protected $table = 'factura_fotos';

    protected $fillable = [
        'factura_id',
        'ruta_foto',
        'descripcion',
        'tipo',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }

    public function getTipoBadgeAttribute()
    {
        return match ($this->tipo) {
            'factura' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Factura</span>',
            'comprobante' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Comprobante</span>',
            'otro' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Otro</span>',
            default => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Desconocido</span>',
        };
    }
}
