<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Auditable;
class Gasto extends Model
{
    use Auditable;

    protected $table = 'gastos';

    protected $fillable = [
        'user_id',
        'motivo',
        'cantidad',
        'fecha',
    ];

    // En tu modelo Auditoria
    protected static function booted(): void
    {
        static::addGlobalScope('tenant_filter', function (Builder $builder) {
            if (auth()->check()) {
                $tenantId = auth()->user()->role === 'admin'
                    ? auth()->user()->id
                    : auth()->user()->tenant_id;

                $builder->where('gastos.tenant_id', $tenantId);
            }
        });
    }


    // En tu modelo Producto
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
