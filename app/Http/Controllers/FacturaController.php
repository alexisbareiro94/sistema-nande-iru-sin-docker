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
            'foto' => 'required|image|max:5120', // 5MB max
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
                    'ruta' => asset('facturas/' . $filename),
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

}