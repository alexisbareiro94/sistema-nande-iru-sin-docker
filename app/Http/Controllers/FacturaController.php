<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\FacturaFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['venta.cliente', 'venta.detalleVentas'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('facturas.index', [
            'facturas' => $facturas
        ]);
    }

    public function show($id)
    {
        $factura = Factura::with([
            'venta.cliente',
            'venta.detalleVentas.producto',
            'venta.vehiculo',
            'venta.vendedor',
            'venta.pagos',
            'fotos'
        ])->findOrFail($id);

        return view('facturas.show', [
            'factura' => $factura
        ]);
    }

    public function anular($id)
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

    public function subirFoto(Request $request, $id)
    {
        $request->validate([
            'foto' => 'required|image|max:5120', // 5MB max
            'tipo' => 'required|in:factura,comprobante,otro',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $factura = Factura::findOrFail($id);

        $file = $request->file('foto');
        $filename = 'factura_' . $id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->move(public_path('facturas'), $filename);

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
    }

    public function eliminarFoto($id)
    {
        $foto = FacturaFoto::findOrFail($id);

        // Eliminar archivo físico
        $filePath = public_path('facturas/' . $foto->ruta_foto);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $foto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto eliminada correctamente'
        ]);
    }
}

