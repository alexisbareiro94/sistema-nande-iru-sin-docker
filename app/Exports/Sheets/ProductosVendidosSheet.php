<?php

namespace App\Exports\Sheets;

use App\Models\DetalleVenta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductosVendidosSheet implements FromArray, WithTitle, WithHeadings
{
    protected Carbon $fechaInicio;
    protected Carbon $fechaFin;

    public function __construct(string $fechaInicio, string $fechaFin)
    {
        $this->fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        $this->fechaFin = Carbon::parse($fechaFin)->endOfDay();
    }

    public function title(): string
    {
        return 'Productos Vendidos';
    }

    public function headings(): array
    {
        return [
            '#',
            'Producto',
            'Categoría',
            'Tipo',
            'Cantidad Vendida',
            'Precio Unitario Promedio',
            'Total Vendido',
            'Costo Total',
            'Utilidad',
        ];
    }

    public function array(): array
    {
        $detalles = DetalleVenta::whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])
            ->with('producto.categoria')
            ->get()
            ->groupBy('producto_id');

        $result = [];
        $index = 1;

        foreach ($detalles as $productoId => $items) {
            $producto = $items->first()->producto;
            if (!$producto)
                continue;

            $cantidadTotal = $items->sum('cantidad');
            $totalVendido = $items->sum('total');
            $precioPromedio = $cantidadTotal > 0 ? $totalVendido / $cantidadTotal : 0;
            $costoTotal = ($producto->precio_compra ?? 0) * $cantidadTotal;
            $utilidad = $totalVendido - $costoTotal;

            $result[] = [
                $index++,
                $producto->nombre,
                $producto->categoria->nombre ?? '-',
                ucfirst($producto->tipo ?? 'producto'),
                $cantidadTotal,
                'Gs. ' . number_format($precioPromedio, 0, ',', '.'),
                'Gs. ' . number_format($totalVendido, 0, ',', '.'),
                'Gs. ' . number_format($costoTotal, 0, ',', '.'),
                'Gs. ' . number_format($utilidad, 0, ',', '.'),
            ];
        }

        // Ordenar por cantidad vendida desc
        usort($result, function ($a, $b) {
            return $b[4] <=> $a[4];
        });

        // Re-indexar
        foreach ($result as $i => &$row) {
            $row[0] = $i + 1;
        }

        return $result;
    }
}
