<?php

namespace App\Exports\Sheets;

use App\Models\ServicioProceso;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MecanicosSheet implements FromArray, WithTitle, WithHeadings
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
        return 'Mecánicos';
    }

    public function headings(): array
    {
        return [
            '#',
            'Nombre',
            'Email',
            'Total Servicios',
            'Pendientes',
            'En Proceso',
            'Completados',
            'Cobrados',
        ];
    }

    public function array(): array
    {
        $servicios = ServicioProceso::whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])
            ->with('mecanico')
            ->get()
            ->groupBy('mecanico_id');

        $result = [];
        $index = 1;

        foreach ($servicios as $mecanicoId => $items) {
            $mecanico = $items->first()->mecanico;
            if (!$mecanico)
                continue;

            $result[] = [
                $index++,
                $mecanico->name ?? '-',
                $mecanico->email ?? '-',
                $items->count(),
                $items->where('estado', 'pendiente')->count(),
                $items->where('estado', 'en_proceso')->count(),
                $items->where('estado', 'completado')->count(),
                $items->where('estado', 'cobrado')->count(),
            ];
        }

        // Ordenar por total de servicios desc
        usort($result, function ($a, $b) {
            return $b[3] <=> $a[3];
        });

        // Re-indexar
        foreach ($result as $i => &$row) {
            $row[0] = $i + 1;
        }

        return $result;
    }
}
