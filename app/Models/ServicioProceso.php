<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ServicioProceso extends Model
{
    protected $table = 'servicios_proceso';

    protected $fillable = [
        'codigo',
        'vehiculo_id',
        'mecanico_id',
        'cliente_id',
        'estado',
        'descripcion',
        'observaciones',
        'fecha_inicio',
        'fecha_fin',
        'venta_id',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant_filter', function (Builder $builder) {
            if (auth()->check()) {
                $tenantId = auth()->user()->role === 'admin'
                    ? auth()->user()->id
                    : auth()->user()->tenant_id;

                $builder->where('servicios_proceso.tenant_id', $tenantId);
            }
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->tenant_id && auth()->check()) {
                $model->tenant_id = auth()->user()->role === 'admin'
                    ? auth()->user()->id
                    : auth()->user()->tenant_id;
            }

            // Generar código único
            if (!$model->codigo) {
                $model->codigo = 'SRV-' . strtoupper(uniqid());
            }
        });
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function fotos()
    {
        return $this->hasMany(ServicioProcesoFoto::class, 'servicio_proceso_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getEstadoBadgeAttribute()
    {
        return match ($this->estado) {
            'pendiente' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>',
            'en_proceso' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">En Proceso</span>',
            'completado' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Completado</span>',
            'cancelado' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Cancelado</span>',
            'cobrado' => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Cobrado</span>',
            default => '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Desconocido</span>',
        };
    }
}
