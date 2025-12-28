<?php

namespace App\Exports\Sheets;

use App\Models\Factura;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FacturasSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
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
        return 'Facturas';
    }

    public function collection()
    {
        return Factura::whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])
            ->with('venta.cliente')
            ->orderBy('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Número',
            'Timbrado',
            'Fecha Emisión',
            'Cliente',
            'Tipo',
            'Condición',
            'Estado',
            'Monto',
        ];
    }

    public function map($factura): array
    {
        return [
            $factura->id,
            $factura->numero_formateado ?? '-',
            $factura->timbrado ?? '-',
            $factura->emision ? $factura->emision->format('d/m/Y') : '-',
            $factura->venta->cliente->razon_social ?? $factura->venta->cliente->name ?? '-',
            ucfirst($factura->tipo ?? '-'),
            ucfirst(str_replace('_', ' ', $factura->condicion_venta ?? '-')),
            ucfirst($factura->estado ?? '-'),
            'Gs. ' . number_format($factura->venta->total ?? 0, 0, ',', '.'),
        ];
    }
}
