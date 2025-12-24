<?php

namespace App\Traits;

use App\Models\Auditoria;

trait Auditable
{
    /**
     * Campos que no se deben registrar en la auditoría
     */
    protected static array $camposExcluidos = [
        'password',
        'remember_token',
        'updated_at',
        'created_at',
    ];

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::auditable('crear', $model);
        });

        static::updated(function ($model) {
            static::auditable(
                'actualizar',
                $model,
                static::filtrarCampos($model->getOriginal()),
                static::filtrarCampos($model->getChanges())
            );
        });

        static::deleted(function ($model) {
            static::auditable('eliminar', $model);
        });
    }

    /**
     * Registrar una acción de auditoría
     */
    protected static function auditable(
        string $accion,
        $model,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null
    ): void {
        $entidadType = get_class($model);
        $modulo = Auditoria::MODULOS[$entidadType] ?? 'general';
        $entidadNombre = Auditoria::ENTIDADES[$entidadType] ?? class_basename($model);

        // Generar descripción automática
        $descripcion = static::generarDescripcion($accion, $model, $entidadNombre);

        // Si es creación, guardar los datos del modelo como datos nuevos
        if ($accion === 'crear') {
            $datosNuevos = static::filtrarCampos($model->getAttributes());
        }

        // Si es eliminación, guardar los datos del modelo como datos anteriores
        if ($accion === 'eliminar') {
            $datosAnteriores = static::filtrarCampos($model->getAttributes());
        }

        Auditoria::create([
            // 'tenant_id' => $model->tenant_id ?? (auth()->user()->tenant_id ?? 1),
            'created_by' => auth()->id() ?? 1,
            'entidad_type' => $entidadType,
            'entidad_id' => $model->id,
            'modulo' => $modulo,
            'accion' => $accion,
            'descripcion' => $descripcion,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
        ]);
    }

    /**
     * Generar descripción legible de la acción
     */
    protected static function generarDescripcion(string $accion, $model, string $entidadNombre): string
    {
        $identificador = static::obtenerIdentificador($model);

        $acciones = [
            'crear' => "Creó {$entidadNombre}",
            'actualizar' => "Actualizó {$entidadNombre}",
            'eliminar' => "Eliminó {$entidadNombre}",
        ];

        $descripcionAccion = $acciones[$accion] ?? ucfirst($accion) . " {$entidadNombre}";

        return $identificador
            ? "{$descripcionAccion}: {$identificador}"
            : $descripcionAccion;
    }

    /**
     * Obtener un identificador legible del modelo
     */
    protected static function obtenerIdentificador($model): ?string
    {
        // Intentar obtener campos comunes de identificación
        $camposIdentificacion = [
            'numero_formateado', // Para facturas
            'name',
            'nombre',
            'razon_social',
            'codigo',
            'placa', // Para vehículos
            'titulo',
        ];

        foreach ($camposIdentificacion as $campo) {
            if (isset($model->{$campo}) && !empty($model->{$campo})) {
                return $model->{$campo};
            }
        }

        // Si no encuentra un campo legible, usar el ID
        return $model->id ? "#{$model->id}" : null;
    }

    /**
     * Filtrar campos sensibles o innecesarios
     */
    protected static function filtrarCampos(array $datos): array
    {
        return array_filter($datos, function ($key) {
            return !in_array($key, static::$camposExcluidos);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Auditar una acción personalizada
     */
    public function auditarAccion(string $accion, ?string $descripcion = null, ?array $datosExtra = null): void
    {
        Auditoria::registrar(
            $accion,
            $this,
            $descripcion,
            null,
            $datosExtra
        );
    }
}