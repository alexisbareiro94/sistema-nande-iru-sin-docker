<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class SalariosExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public $salarios;
    public $totalPagado;
    public $totalRestante;
    public $totalNeto;

    public function __construct($pagos)
    {
        $this->salarios = $pagos;
        $this->totalPagado = $pagos->sum('monto');
        $this->totalRestante = $pagos->sum('restante');
        $this->totalNeto = $pagos->map(fn($pago) => $pago->user->sum('salario'));
    }

    public function collection()
    {
        return $this->salarios;
    }

    public function headings(): array
    {
        return [
            'Empleado',
            'Cargo',
            'Pago',
            'Restante',
            'Salario Total',
            'Fecha Pago',
        ];
    }

    public function map($salario): array
    {
        return [
            $salario->user->name,
            'Personal',
            $salario->monto,
            $salario->restante,
            $salario->user->salario,
            $salario->created_at,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Escribir encabezado personalizado antes de los títulos
                $sheet->setCellValue('A1', 'Reporte de Pagos de Salarios');
                $sheet->setCellValue('A2', 'Total Pagado:');
                $sheet->setCellValue('B2', $this->totalPagado);

                $sheet->setCellValue('A3', 'Total Restante:');
                $sheet->setCellValue('B3', $this->totalRestante);

                $sheet->setCellValue('A4', 'Neto a Pagar:');
                $sheet->setCellValue('B4', $this->totalNeto[0]);

                $sheet->setCellValue('A5', 'Fecha de generación:');
                $sheet->setCellValue('B5', now()->format('d/m/Y H:i'));

                // Dejar una fila vacía y luego poner las cabeceras
                $sheet->insertNewRowBefore(6);
            },
        ];
    }
}
