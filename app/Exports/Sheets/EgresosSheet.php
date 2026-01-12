<?php

namespace App\Exports\Sheets;

use App\Models\MovimientoCaja;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EgresosSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
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
        return 'Egresos';
    }

    public function collection()
    {
        return MovimientoCaja::where('tipo', 'egreso')
            ->whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])
            ->with('caja.user')
            ->orderBy('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Fecha',
            'Concepto',
            'Responsable',
            'Monto',
        ];
    }

    public function map($movimiento): array
    {
        return [
            $movimiento->id,
            $movimiento->created_at->format('d/m/Y H:i'),
            $movimiento->concepto ?? '-',
            $movimiento->caja->user->name ?? '-',
            'Gs. ' . number_format($movimiento->monto, 0, ',', '.'),
        ];
    }
}
