<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Auditoria extends Model
{
    protected $table = 'auditorias';

    protected $fillable = [
        'tenant_id',
        'created_by',
        'entidad_type',
        'entidad_id',
        'modulo',
        'accion',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
    ];

    /**
     * Mapeo de modelos a nombres legibles
     */
    public const MODULOS = [
        'App\Models\Venta' => 'ventas',
        'App\Models\Factura' => 'facturas',
        'App\Models\FacturaFoto' => 'facturas',
        'App\Models\Producto' => 'inventario',
        'App\Models\Categoria' => 'inventario',
        'App\Models\Marca' => 'inventario',
        'App\Models\Cliente' => 'clientes',
        'App\Models\Vehiculo' => 'vehiculos',
        'App\Models\ServicioProceso' => 'servicios',
        'App\Models\ServicioProcesoFoto' => 'servicios',
        'App\Models\User' => 'usuarios',
        'App\Models\Caja' => 'caja',
        'App\Models\MovimientoCaja' => 'caja',
        'App\Models\PagoSalario' => 'salarios',
        'App\Models\Gasto' => 'gastos',
        'App\Models\Distribuidor' => 'distribuidores',
        'App\Models\MetodoPago' => 'configuracion',
    ];

    /**
     * Mapeo de modelos a nombres en español
     */
    public const ENTIDADES = [
        'App\Models\Venta' => 'Venta',
        'App\Models\Factura' => 'Factura',
        'App\Models\FacturaFoto' => 'Foto de Factura',
        'App\Models\Producto' => 'Producto',
        'App\Models\Categoria' => 'Categoría',
        'App\Models\Marca' => 'Marca',
        'App\Models\Cliente' => 'Cliente',
        'App\Models\Vehiculo' => 'Vehículo',
        'App\Models\ServicioProceso' => 'Servicio',
        'App\Models\ServicioProcesoFoto' => 'Foto de Servicio',
        'App\Models\User' => 'Usuario',
        'App\Models\Caja' => 'Caja',
        'App\Models\MovimientoCaja' => 'Movimiento de Caja',
        'App\Models\PagoSalario' => 'Pago de Salario',
        'App\Models\Gasto' => 'Gasto',
        'App\Models\Distribuidor' => 'Distribuidor',
        'App\Models\MetodoPago' => 'Método de Pago',
    ];

    /**
     * Colores para cada tipo de acción
     */
    public const ACCIONES_COLORES = [
        'crear' => 'green',
        'actualizar' => 'blue',
        'eliminar' => 'red',
        'login' => 'indigo',
        'logout' => 'gray',
        'anular' => 'orange',
        'restaurar' => 'teal',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant_filter', function (Builder $builder) {
            if (auth()->check()) {
                $tenantId = auth()->user()->role === 'admin'
                    ? auth()->user()->id
                    : auth()->user()->tenant_id;

                $builder->where('auditorias.tenant_id', $tenantId);
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

            // Capturar IP y User Agent automáticamente
            if (!$model->ip_address) {
                $model->ip_address = request()->ip();
            }
            if (!$model->user_agent) {
                $model->user_agent = request()->userAgent();
            }
        });
    }

    /**
     * Relación con el usuario que realizó la acción
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación morfológica con la entidad
     */
    public function entidad()
    {
        return $this->morphTo();
    }

    /**
     * Obtener el nombre legible del módulo
     */
    public function getModuloLegibleAttribute(): string
    {
        return ucfirst($this->modulo ?? 'general');
    }

    /**
     * Obtener el nombre legible del tipo de entidad
     */
    public function getEntidadLegibleAttribute(): string
    {
        return self::ENTIDADES[$this->entidad_type] ?? class_basename($this->entidad_type);
    }

    /**
     * Obtener el color de la acción
     */
    public function getAccionColorAttribute(): string
    {
        return self::ACCIONES_COLORES[$this->accion] ?? 'gray';
    }

    /**
     * Obtener badge HTML para la acción
     */
    public function getAccionBadgeAttribute(): string
    {
        $colores = [
            'green' => 'bg-green-100 text-green-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'red' => 'bg-red-100 text-red-800',
            'indigo' => 'bg-indigo-100 text-indigo-800',
            'gray' => 'bg-gray-100 text-gray-800',
            'orange' => 'bg-orange-100 text-orange-800',
            'teal' => 'bg-teal-100 text-teal-800',
        ];

        $color = $this->accion_color;
        $clase = $colores[$color] ?? $colores['gray'];

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $clase . '">' . ucfirst($this->accion) . '</span>';
    }

    /**
     * Obtener los campos que cambiaron (para actualizaciones)
     */
    public function getCamposCambiadosAttribute(): array
    {
        if (!$this->datos_anteriores || !$this->datos_nuevos) {
            return [];
        }

        $cambios = [];
        foreach ($this->datos_nuevos as $campo => $valorNuevo) {
            if (isset($this->datos_anteriores[$campo]) && $this->datos_anteriores[$campo] !== $valorNuevo) {
                $cambios[$campo] = [
                    'antes' => $this->datos_anteriores[$campo],
                    'despues' => $valorNuevo,
                ];
            }
        }

        return $cambios;
    }

    /**
     * Crear registro de auditoría de forma estática
     */
    public static function registrar(
        string $accion,
        $entidad,
        ?string $descripcion = null,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null
    ): self {
        $entidadType = $entidad instanceof Model ? get_class($entidad) : $entidad;
        $entidadId = $entidad instanceof Model ? $entidad->id : null;

        return self::create([
            'created_by' => auth()->id(),
            'entidad_type' => $entidadType,
            'entidad_id' => $entidadId,
            'modulo' => self::MODULOS[$entidadType] ?? 'general',
            'accion' => $accion,
            'descripcion' => $descripcion,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
        ]);
    }
}
