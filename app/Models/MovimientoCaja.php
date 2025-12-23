<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Auditable;
class MovimientoCaja extends Model
{
    use HasFactory, Auditable;
    protected $table = 'movimiento_cajas';

    protected $fillable = [
        'caja_id',
        'venta_id',
        'tipo',
        'concepto',
        'monto',
        'venta_anulado'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant_filter', function (Builder $builder) {
            if (auth()->check()) {
                $tenantId = auth()->user()->role === 'admin'
                    ? auth()->user()->id
                    : auth()->user()->tenant_id;

                $builder->where('movimiento_cajas.tenant_id', $tenantId);
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

    public function caja()
    {
        return $this->belongsTo(Caja::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function pagos_salarios()
    {
        return $this->hasOne(PagoSalario::class, 'movimiento_id');
    }
}
