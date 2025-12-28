<?php

namespace App\Exports\Sheets;

use App\Models\Venta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VentasSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
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
        return 'Ventas';
    }

    public function collection()
    {
        return Venta::whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])
            ->with(['cliente', 'vehiculo', 'vendedor', 'detalleVentas.producto'])
            ->orderBy('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Código',
            'Fecha',
            'Cliente',
            'Vehículo',
            'Vendedor',
            'Productos',
            'Forma Pago',
            'Subtotal',
            'Descuento',
            'Total',
        ];
    }

    public function map($venta): array
    {
        $productos = $venta->detalleVentas->map(function ($detalle) {
            return $detalle->producto->nombre . ' x' . $detalle->cantidad;
        })->join(', ');

        return [
            $venta->id,
            $venta->codigo ?? '-',
            $venta->created_at->format('d/m/Y H:i'),
            $venta->cliente->razon_social ?? $venta->cliente->name ?? 'Sin cliente',
            $venta->vehiculo ? $venta->vehiculo->patente . ' - ' . $venta->vehiculo->marca : '-',
            $venta->vendedor->name ?? '-',
            $productos ?: '-',
            ucfirst($venta->forma_pago ?? '-'),
            'Gs. ' . number_format($venta->subtotal ?? $venta->total, 0, ',', '.'),
            'Gs. ' . number_format($venta->monto_descuento ?? 0, 0, ',', '.'),
            'Gs. ' . number_format($venta->total, 0, ',', '.'),
        ];
    }
}
