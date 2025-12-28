<?php

namespace App\Exports;

use App\Exports\Sheets\ResumenSheet;
use App\Exports\Sheets\VentasSheet;
use App\Exports\Sheets\ProductosVendidosSheet;
use App\Exports\Sheets\EgresosSheet;
use App\Exports\Sheets\VehiculosSheet;
use App\Exports\Sheets\MecanicosSheet;
use App\Exports\Sheets\FacturasSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class ReporteGlobalExport implements WithMultipleSheets
{
    use Exportable;

    protected string $fechaInicio;
    protected string $fechaFin;

    public function __construct(string $fechaInicio, string $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function sheets(): array
    {
        return [
            'Resumen' => new ResumenSheet($this->fechaInicio, $this->fechaFin),
            'Ventas' => new VentasSheet($this->fechaInicio, $this->fechaFin),
            'Productos Vendidos' => new ProductosVendidosSheet($this->fechaInicio, $this->fechaFin),
            'Egresos' => new EgresosSheet($this->fechaInicio, $this->fechaFin),
            'Vehículos' => new VehiculosSheet($this->fechaInicio, $this->fechaFin),
            'Mecánicos' => new MecanicosSheet($this->fechaInicio, $this->fechaFin),
            'Facturas' => new FacturasSheet($this->fechaInicio, $this->fechaFin),
        ];
    }
}
