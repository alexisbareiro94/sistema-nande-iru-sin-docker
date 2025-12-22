<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\User;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Vehiculo::with(['cliente', 'mecanico', 'ultimaVenta'])
            ->withCount('ventas as servicios_count');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('patente', 'like', "%{$search}%")
                    ->orWhere('marca', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%");
            });
        }

        $vehiculos = $query->orderByDesc('updated_at')->paginate(20);

        $mecanicos = User::where('role', 'mecanico')
            ->where('tenant_id', tenant_id())
            ->get();

        $clientes = User::whereIn('role', ['cliente', 'mecanico'])
            ->where('tenant_id', tenant_id())
            ->get();

        return view('vehiculos.index', [
            'vehiculos' => $vehiculos,
            'mecanicos' => $mecanicos,
            'clientes' => $clientes,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'patente' => 'required|string|max:10',
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:100',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:30',
            'cliente_id' => 'nullable|exists:users,id',
            'mecanico_id' => 'nullable|exists:users,id',
            'kilometraje' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string',
        ]);

        // Normalizar patente a mayúsculas sin espacios
        $data['patente'] = strtoupper(str_replace(' ', '', $data['patente']));

        // Verificar si ya existe la patente
        $existente = Vehiculo::where('patente', $data['patente'])->first();
        if ($existente) {
            return response()->json([
                'success' => false,
                'error' => 'Ya existe un vehículo con esa patente',
                'vehiculo' => $existente->load(['cliente', 'mecanico']),
            ], 422);
        }

        try {
            $vehiculo = Vehiculo::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Vehículo registrado correctamente',
                'vehiculo' => $vehiculo->load(['cliente', 'mecanico']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        $vehiculo = Vehiculo::with([
            'cliente',
            'mecanico',
            'ventas' => function ($query) {
                $query->with(['productos', 'vendedor', 'caja.user'])
                    ->orderByDesc('created_at');
            }
        ])->findOrFail($id);

        $totalServicios = $vehiculo->ventas->count();
        $totalGastado = $vehiculo->ventas->sum('total');
        $ultimoServicio = $vehiculo->ventas->first();

        return view('vehiculos.show', [
            'vehiculo' => $vehiculo,
            'totalServicios' => $totalServicios,
            'totalGastado' => $totalGastado,
            'ultimoServicio' => $ultimoServicio,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $vehiculo = Vehiculo::findOrFail($id);

        $data = $request->validate([
            'patente' => 'required|string|max:10|unique:vehiculos,patente,' . $id,
            'marca' => 'required|string|max:50',
            'modelo' => 'required|string|max:100',
            'anio' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:30',
            'cliente_id' => 'nullable|exists:users,id',
            'mecanico_id' => 'nullable|exists:users,id',
            'kilometraje' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string',
        ]);

        try {
            $vehiculo->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Vehículo actualizado correctamente',
                'vehiculo' => $vehiculo->fresh(['cliente', 'mecanico']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buscar vehículo por patente (para autocompletado)
     */
    public function buscarPorPatente(Request $request)
    {
        $patente = strtoupper(str_replace(' ', '', $request->query('patente', '')));

        if (strlen($patente) < 2) {
            return response()->json([]);
        }

        $vehiculos = Vehiculo::where('patente', 'like', "{$patente}%")
            ->with(['cliente', 'mecanico'])
            ->limit(10)
            ->get();

        return response()->json($vehiculos);
    }

    /**
     * Obtener vehículo exacto por patente
     */
    public function obtenerPorPatente(Request $request)
    {
        $patente = strtoupper(str_replace(' ', '', $request->query('patente', '')));

        $vehiculo = Vehiculo::where('patente', $patente)
            ->with(['cliente', 'mecanico'])
            ->first();

        if (!$vehiculo) {
            return response()->json([
                'success' => false,
                'encontrado' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'encontrado' => true,
            'vehiculo' => $vehiculo,
        ]);
    }
}
