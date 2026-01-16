<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\FacturaFoto;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yaza\LaravelGoogleDriveStorage\Gdrive;
use Storage;

class FacturaController extends Controller
{
    public function index(): View
    {
        $facturas = Factura::with(['venta.cliente', 'venta.detalleVentas'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('facturas.index', [
            'facturas' => $facturas
        ]);
    }

    public function show(string $id): View|RedirectResponse
    {
        try {
            $factura = Factura::with([
                'venta.cliente',
                'venta.detalleVentas.producto',
                'venta.vehiculo',
                'venta.vendedor',
                'venta.pagos',
                // 'fotos'
            ])->findOrFail($id);

            return view('facturas.show', [
                'factura' => $factura,

            ]);
        } catch (\Exception $e) {
            return redirect()->route('facturas.index')->with('error', 'Factura no encontrada');
        }
    }

    public function anular(string $id): JsonResponse
    {
        $factura = Factura::findOrFail($id);

        if ($factura->estado === 'anulada') {
            return response()->json([
                'success' => false,
                'message' => 'La factura ya está anulada'
            ], 400);
        }

        $factura->update(['estado' => 'anulada']);

        return response()->json([
            'success' => true,
            'message' => 'Factura anulada correctamente'
        ]);
    }

    public function subirFoto(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'foto' => 'required|image|max:5120',
            'tipo' => 'required|in:factura,comprobante,otro',
            'descripcion' => 'nullable|string|max:255',
        ]);

        try {
            $factura = Factura::findOrFail($id);

            $file = $request->file('foto');
            $filename = time() . '.' . 'jpg';
            Gdrive::put($filename, $file);

            $foto = FacturaFoto::create([
                'factura_id' => $factura->id,
                'ruta_foto' => $filename,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto subida correctamente',
                'foto' => [
                    'id' => $foto->id,
                    'descripcion' => $foto->descripcion,
                    'tipo_badge' => $foto->tipo_badge,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getImages(string $id): JsonResponse
    {
        try {
            $factura = Factura::findOrFail($id);

            $res = Gdrive::all('/');

            // Crear un mapa de ruta_foto => foto para acceder a los datos
            $fotosMap = $factura->fotos->keyBy('ruta_foto');

            $images = $res
                ->filter(fn($item) => isset($fotosMap[$item->path()]))
                ->map(function ($item) use ($fotosMap) {
                    $foto = $fotosMap[$item->path()];
                    return [
                        'id' => $foto->id,
                        'path' => $item->path(),
                        'url' => url('/gdrive-image/' . $item->path()),
                        'tipo' => $foto->tipo,
                        'descripcion' => $foto->descripcion,
                    ];
                })
                ->values();
            return response()->json($images);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showImage(string $path): Response
    {
        try {
            $disk = Storage::disk('google');

            if (!$disk->exists($path)) {
                abort(404);
            }

            return response(
                $disk->get($path),
                200,
                ['Content-Type' => $disk->mimeType($path)]
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function eliminarFoto(string $id): JsonResponse
    {
        try {
            $foto = FacturaFoto::findOrFail($id);
            // Eliminar archivo físico
            Gdrive::delete($foto->ruta_foto);
            $foto->delete();
            return response()->json([
                'success' => true,
                'message' => 'Foto eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Establecer número de factura inicial en session
     */
    public function setNumeroInicial(Request $request): JsonResponse
    {
        $request->validate([
            'numero' => 'required|integer|min:1',
        ]);

        session(['numero_factura_inicial' => $request->numero]);

        return response()->json([
            'success' => true,
            'message' => 'Número de factura configurado correctamente',
            'numero' => $request->numero
        ]);
    }

    /**
     * Obtener número de factura configurado en session
     */
    public function getNumeroInicial(): JsonResponse
    {
        $numero = session('numero_factura_inicial');

        return response()->json([
            'success' => true,
            'numero' => $numero,
            'existe' => $numero !== null
        ]);
    }

    /**
     * Eliminar configuración de número de factura de session
     */
    public function clearNumeroInicial(): JsonResponse
    {
        session()->forget('numero_factura_inicial');

        return response()->json([
            'success' => true,
            'message' => 'Configuración eliminada'
        ]);
    }

    /**
     * Establecer timbrado en session
     */
    public function setTimbrado(Request $request): JsonResponse
    {
        $request->validate([
            'timbrado' => 'required|integer|min:1',
        ]);

        session(['timbrado_factura' => $request->timbrado]);

        return response()->json([
            'success' => true,
            'message' => 'Timbrado configurado correctamente',
            'timbrado' => $request->timbrado
        ]);
    }

    /**
     * Obtener timbrado configurado en session
     */
    public function getTimbrado(): JsonResponse
    {
        $timbrado = session('timbrado_factura');

        return response()->json([
            'success' => true,
            'timbrado' => $timbrado,
            'existe' => $timbrado !== null
        ]);
    }

    /**
     * Eliminar configuración de timbrado de session
     */
    public function clearTimbrado(): JsonResponse
    {
        session()->forget('timbrado_factura');

        return response()->json([
            'success' => true,
            'message' => 'Configuración de timbrado eliminada'
        ]);
    }

    /**
     * Buscar venta por código que no tenga factura asociada
     */
    public function buscarVenta(Request $request): JsonResponse
    {
        $codigo = $request->get('codigo');

        if (!$codigo) {
            return response()->json([
                'success' => false,
                'message' => 'Código de venta requerido'
            ], 400);
        }

        $venta = \App\Models\Venta::with('cliente')
            ->where('codigo', 'like', "%{$codigo}%")
            ->whereDoesntHave('factura')
            ->first();

        if (!$venta) {
            return response()->json([
                'success' => false,
                'message' => 'Venta no encontrada o ya tiene factura asociada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'venta' => [
                'id' => $venta->id,
                'codigo' => $venta->codigo,
                'total' => number_format($venta->total, 0, ',', '.'),
                'fecha' => $venta->created_at->format('d/m/Y H:i'),
                'cliente_actual' => $venta->cliente?->razon_social ?? $venta->cliente?->name ?? 'Sin cliente',
            ]
        ]);
    }

    /**
     * Buscar clientes por nombre, razón social o RUC
     */
    public function buscarClientes(Request $request): JsonResponse
    {
        $query = $request->get('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $clientes = \App\Models\User::where('role', 'cliente')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('razon_social', 'like', "%{$query}%")
                    ->orWhere('ruc_ci', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'razon_social', 'ruc_ci']);

        return response()->json($clientes);
    }

    /**
     * Asociar factura a una venta existente
     * Actualiza el cliente_id de la venta y crea la factura
     */
    public function asociarFactura(Request $request): JsonResponse
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'cliente_id' => 'required|exists:users,id',
        ]);

        try {
            $venta = \App\Models\Venta::findOrFail($request->venta_id);

            // Verificar que la venta no tenga factura
            if ($venta->factura) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venta ya tiene una factura asociada'
                ], 400);
            }

            // Actualizar el cliente_id de la venta
            $venta->update(['cliente_id' => $request->cliente_id]);

            // Obtener el último número de factura
            $ultimaFactura = Factura::orderBy('numero', 'desc')->first();

            // Verificar si hay un número inicial configurado en sesión
            $numeroFacturaInicial = session('numero_factura_inicial');
            if ($numeroFacturaInicial !== null) {
                $nuevoNumero = $numeroFacturaInicial;
                session()->forget('numero_factura_inicial');
            } else {
                $nuevoNumero = $ultimaFactura?->numero !== null ? $ultimaFactura->numero + 1 : 87;
            }

            // Obtener timbrado de sesión o usar el por defecto
            $timbradoSession = session('timbrado_factura');
            $timbrado = $timbradoSession !== null ? $timbradoSession : 18450157;

            // Crear la factura
            $factura = Factura::create([
                'venta_id' => $venta->id,
                'timbrado' => $timbrado,
                'sucursal' => 001,
                'punto_emision' => 001,
                'numero' => $nuevoNumero,
                'emision' => now()->format('Y-m-d'),
                'estado' => 'emitida',
                'tipo' => 'factura',
                'condicion_venta' => 'contado',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Factura asociada correctamente',
                'factura' => [
                    'id' => $factura->id,
                    'numero_formateado' => $factura->numero_formateado,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asociar la factura: ' . $e->getMessage()
            ], 500);
        }
    }

}