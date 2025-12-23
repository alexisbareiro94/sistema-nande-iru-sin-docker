<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    protected $casts = [
        'emision' => 'datetime',
    ];

    // Tenant scope
    protected static function booted(): void
    {
        static::addGlobalScope('tenant_filter', function (Builder $builder) {
            if (auth()->check()) {
                $tenantId = auth()->user()->role === 'admin'
                    ? auth()->user()->id
                    : auth()->user()->tenant_id;

                $builder->whereHas('venta', function ($q) use ($tenantId) {
                    $q->where('ventas.tenant_id', $tenantId);
                });
            }
        });
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function fotos()
    {
        return $this->hasMany(FacturaFoto::class, 'factura_id');
    }

    /**
     * Formato: 001-001 0000089
     */
    public function getNumeroFormateadoAttribute(): string
    {
        $sucursal = str_pad($this->sucursal, 3, '0', STR_PAD_LEFT);
        $puntoEmision = str_pad($this->punto_emision, 3, '0', STR_PAD_LEFT);
        $numero = str_pad($this->numero, 7, '0', STR_PAD_LEFT);

        return "{$sucursal}-{$puntoEmision} {$numero}";
    }

    public function getEstadoBadgeAttribute(): string
    {
        return match ($this->estado) {
            'emitida' => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-green-500 text-white">Emitida</span>',
            'anulada' => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-red-500 text-white">Anulada</span>',
            default => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-500 text-white">Desconocido</span>',
        };
    }

    public function getTipoBadgeAttribute(): string
    {
        return match ($this->tipo) {
            'factura' => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-500 text-white">Factura</span>',
            'nota_debito' => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-orange-500 text-white">Nota Débito</span>',
            'nota_credito' => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-purple-500 text-white">Nota Crédito</span>',
            default => '<span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-500 text-white">Desconocido</span>',
        };
    }
}

