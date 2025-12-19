<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    protected $fillable = [
        'patente',
        'marca',
        'modelo',
        'anio',
        'color',
        'cliente_id',
        'mecanico_id',
        'kilometraje',
        'observaciones',
        'tenant_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant_filter', function (Builder $builder) {
            if (auth()->check()) {
                $tenantId = auth()->user()->role === 'admin'
                    ? auth()->user()->id
                    : auth()->user()->tenant_id;

                $builder->where('vehiculos.tenant_id', $tenantId);
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
        });
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'vehiculo_id');
    }

    public function ultimaVenta()
    {
        return $this->hasOne(Venta::class, 'vehiculo_id')->latest('created_at');
    }

    public function servicioProceso()
    {
        return $this->hasOne(ServicioProceso::class, 'vehiculo_id');
    }

    public function getServiciosCountAttribute()
    {
        return $this->ventas()->count();
    }
}
