<?php

namespace App\Exports\Sheets;

use App\Models\ServicioProceso;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VehiculosSheet implements FromArray, WithTitle, WithHeadings
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
        return 'Vehículos';
    }

    public function headings(): array
    {
        return [
            '#',
            'Patente',
            'Marca',
            'Modelo',
            'Año',
            'Color',
            'Cliente',
            'Servicios en Período',
            'Estado Último Servicio',
        ];
    }

    public function array(): array
    {
        $servicios = ServicioProceso::whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])
            ->with(['vehiculo.cliente', 'mecanico'])
            ->get()
            ->groupBy('vehiculo_id');

        $result = [];
        $index = 1;

        foreach ($servicios as $vehiculoId => $items) {
            $vehiculo = $items->first()->vehiculo;
            if (!$vehiculo)
                continue;

            $ultimoServicio = $items->sortByDesc('created_at')->first();

            $result[] = [
                $index++,
                $vehiculo->patente ?? '-',
                $vehiculo->marca ?? '-',
                $vehiculo->modelo ?? '-',
                $vehiculo->anio ?? '-',
                $vehiculo->color ?? '-',
                $vehiculo->cliente->razon_social ?? $vehiculo->cliente->name ?? '-',
                $items->count(),
                ucfirst(str_replace('_', ' ', $ultimoServicio->estado ?? '-')),
            ];
        }

        return $result;
    }
}
