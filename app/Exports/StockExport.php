<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Producto;
use Illuminate\Contracts\Queue\ShouldQueue;

class StockExport implements FromCollection, WithHeadings, WithMapping, ShouldQueue
{
    public $productos;

    public function __construct()
    {
        try {
            $this->productos = Producto::all();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function collection()
    {
        return $this->productos;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Descripción',
            'Precio Compra',
            'Precio Venta',
            'Stock',
            'Categoría',
            'Ventas',
            'Creado el',
            'Actualizado el',
        ];
    }

    public function map($producto): array
    {
        return [
            $producto->id,
            $producto->nombre,
            $producto->descripcion,
            $producto->precio_compra,
            $producto->precio_venta,
            $producto->stock,
            $producto->categoria->nombre ?? 'Sin categoría',
            $producto->ventas,
            $producto->created_at ? $producto->created_at->format('d/m/Y H:i') : '',
            $producto->updated_at ? $producto->updated_at->format('d/m/Y H:i') : '',
        ];
    }
}
