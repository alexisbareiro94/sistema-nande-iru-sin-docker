<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioProcesoFoto extends Model
{
    protected $table = 'servicio_proceso_fotos';

    protected $fillable = [
        'servicio_proceso_id',
        'ruta_foto',
        'descripcion',
        'tipo',
    ];

    public function servicioProceso()
    {
        return $this->belongsTo(ServicioProceso::class, 'servicio_proceso_id');
    }

    public function getTipoBadgeAttribute()
    {
        return match ($this->tipo) {
            'ingreso' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Ingreso</span>',
            'proceso' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Proceso</span>',
            'entrega' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Entrega</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">-</span>',
        };
    }
}
