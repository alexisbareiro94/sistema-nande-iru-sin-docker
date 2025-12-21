<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ServicioProceso;
use App\Models\ServicioProcesoFoto;
use App\Models\Vehiculo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicioProcesoController extends Controller
{
    public function index()
    {
        $tenantId = tenant_id();

        $servicios = ServicioProceso::with(['vehiculo', 'mecanico', 'cliente'])
            ->orderByDesc('created_at')
            ->get();

        $mecanicos = User::where('role', 'mecanico')
            ->where('activo', true)
            ->where('tenant_id', $tenantId)
            ->get();

        return view('servicio-proceso.index', [
            'servicios' => $servicios,
            'mecanicos' => $mecanicos,
        ]);
    }

    /**
     * Mostrar detalle de un servicio
     */
    public function show(string $id)
    {
        $tenantId = tenant_id();

        $servicio = ServicioProceso::with(['vehiculo.cliente', 'mecanico', 'cliente', 'fotos'])
            ->findOrFail($id);

        $clientes = User::where('role', 'cliente')
            ->where('tenant_id', $tenantId)
            ->get();

        $vehiculos = Vehiculo::where('tenant_id', $tenantId)
            ->with('servicioProceso')
            ->get();


        $mecanicos = User::where('role', 'mecanico')
            ->where('activo', true)
            ->where('tenant_id', $tenantId)
            ->get();

        return view('servicio-proceso.show', [
            'servicio' => $servicio,
            'mecanicos' => $mecanicos,
            'clientes' => $clientes,
            'vehiculos' => $vehiculos,
        ]);
    }

    /**
     * Crear nuevo servicio en proceso
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'mecanico_id' => 'nullable|exists:users,id',
            'descripcion' => 'nullable|string|max:1000',
            'tipo_servicio_id' => 'nullable|exists:productos,id',
        ]);

        try {
            $vehiculo = null;
            $clienteId = null;

            if ($request->vehiculo_id) {
                $vehiculo = Vehiculo::find($request->vehiculo_id);
                $clienteId = $vehiculo?->cliente_id;
            }

            $servicio = ServicioProceso::create([
                'vehiculo_id' => $request->vehiculo_id,
                'mecanico_id' => $request->mecanico_id,
                'cliente_id' => $clienteId,
                'descripcion' => $request->descripcion,
                'tipo_servicio_id' => $request->tipo_servicio_id,
                'estado' => 'pendiente',
                'fecha_inicio' => now(),
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Servicio creado correctamente',
                'servicio' => $servicio,
                'redirect' => route('servicio.proceso.show', $servicio->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar servicio
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'mecanico_id' => 'nullable|exists:users,id',
            'cliente_id' => 'nullable|exists:users,id',
            'estado' => 'nullable|in:pendiente,en_proceso,completado,cancelado',
            'descripcion' => 'nullable|string|max:1000',
            'observaciones' => 'nullable|string|max:2000',
        ]);

        if (filled($request->vehiculo_id)) {
            $vehiculo = ServicioProceso::where('vehiculo_id', $request->vehiculo_id)
                ->whereIn('estado', ['pendiente', 'en_proceso'])
                ->first();

            if (filled($vehiculo)) {
                return response()->json([
                    'success' => false,
                    'error' => 'El vehículo ya tiene un servicio en proceso',
                ]);
            }
        }

        try {
            $servicio = ServicioProceso::findOrFail($id);
            $request->updated_by = auth()->user()->id;

            if ($request->estado === 'completado' && $servicio->estado !== 'completado') {
                $request->fecha_fin = now();
            }

            // Si se cambia el vehículo, actualizar cliente
            if ($request->vehiculo_id && $request->vehiculo_id != $servicio->vehiculo_id) {
                $vehiculo = Vehiculo::find($request->vehiculo_id);
                $request->cliente_id = $vehiculo?->cliente_id;
            }

            $servicio->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Servicio actualizado correctamente',
                'servicio' => $servicio->fresh(['vehiculo', 'mecanico', 'cliente']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Subir foto al servicio
     */
    public function subirFoto(Request $request, string $id)
    {
        $request->validate([
            'foto' => 'required|image|max:5120', // Max 5MB
            'descripcion' => 'nullable|string|max:500',
            'tipo' => 'required|in:ingreso,proceso,entrega',
        ]);

        try {
            $servicio = ServicioProceso::findOrFail($id);

            // Guardar la foto
            $path = now()->format('YmdHis') . '.' . $request->file('foto')->getClientOriginalExtension();
            $request->file('foto')->move(public_path('servicios'), $path);

            $foto = ServicioProcesoFoto::create([
                'servicio_proceso_id' => $servicio->id,
                'ruta_foto' => $path,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto subida correctamente',
                'foto' => $foto,
                'url' => $path,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar foto
     */
    public function eliminarFoto(string $id)
    {
        try {
            $foto = ServicioProcesoFoto::findOrFail($id);

            // Eliminar archivo
            if (file_exists(public_path('servicios/' . $foto->ruta_foto))) {
                unlink(public_path('servicios/' . $foto->ruta_foto));
            }

            $foto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Foto eliminada correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buscar vehículo por patente (API)
     */
    public function buscarVehiculo(Request $request)
    {
        $patente = $request->get('patente');

        if (!$patente) {
            return response()->json([
                'success' => false,
                'error' => 'Patente requerida',
            ], 400);
        }

        $vehiculo = Vehiculo::with('cliente')
            ->where('patente', 'like', "%{$patente}%")
            ->first();

        if (!$vehiculo) {
            return response()->json([
                'success' => false,
                'exists' => false,
                'message' => 'Vehículo no encontrado',
            ]);
        }

        return response()->json([
            'success' => true,
            'exists' => true,
            'vehiculo' => $vehiculo,
        ]);
    }

    /**
     * Obtener servicios activos (API para dashboard/caja)
     */
    public function serviciosActivos()
    {
        $servicios = ServicioProceso::with(['vehiculo', 'mecanico'])
            ->whereIn('estado', ['pendiente', 'en_proceso'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'servicios' => $servicios,
            'total' => $servicios->count(),
        ]);
    }

    /**
     * Crear vehículo desde el servicio
     */
    public function crearVehiculo(Request $request)
    {
        $request->validate([
            'patente' => 'required|string|max:10',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:100',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:30',
            'kilometraje' => 'nullable|integer|min:0',
            'cliente_id' => 'nullable|exists:users,id',
        ]);

        try {
            $vehiculo = Vehiculo::create([
                'patente' => strtoupper($request->patente),
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'anio' => $request->anio,
                'color' => $request->color,
                'kilometraje' => $request->kilometraje,
                'cliente_id' => $request->cliente_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vehículo creado correctamente',
                'vehiculo' => $vehiculo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear cliente desde el servicio
     */
    public function crearCliente(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc_ci' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        try {
            $cliente = User::create([
                'name' => $request->razon_social,
                'razon_social' => $request->razon_social,
                'ruc_ci' => $request->ruc_ci,
                'telefono' => $request->telefono,
                'email' => $request->email ?? 'cliente_' . uniqid() . '@temp.com',
                'password' => bcrypt('temporal123'),
                'role' => 'cliente',
                'activo' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado correctamente',
                'cliente' => $cliente,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Crear mecánico desde el servicio
     */
    public function crearMecanico(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'ruc_ci' => 'nullable|string|max:20',
            'salario' => 'nullable|integer|min:0',
        ]);

        try {
            $mecanico = User::create([
                'name' => $request->name,
                'telefono' => $request->telefono,
                'ruc_ci' => $request->ruc_ci,
                'salario' => $request->salario,
                'email' => 'mecanico_' . uniqid() . '@temp.com',
                'password' => bcrypt('temporal123'),
                'role' => 'mecanico',
                'activo' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mecánico creado correctamente',
                'mecanico' => $mecanico,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
