<?php

namespace App\Exports\Sheets;

use App\Models\Venta;
use App\Models\Pago;
use App\Models\MovimientoCaja;
use App\Models\DetalleVenta;
use App\Models\ServicioProceso;
use App\Models\Factura;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResumenSheet implements FromArray, WithTitle, WithHeadings, WithStyles
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
        return 'Resumen';
    }

    public function headings(): array
    {
        return [
            'Concepto',
            'Valor',
        ];
    }

    public function array(): array
    {
        // Ventas
        $ventas = Venta::whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])->get();
        $totalVentas = $ventas->sum('total');
        $cantidadVentas = $ventas->count();

        // Detalles para calcular costo
        $detalles = DetalleVenta::whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])
            ->with('producto')
            ->get();

        $costoTotal = 0;
        foreach ($detalles as $detalle) {
            $costoTotal += ($detalle->producto->precio_compra ?? 0) * $detalle->cantidad;
        }

        // Egresos
        $egresos = MovimientoCaja::where('tipo', 'egreso')
            ->whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])
            ->sum('monto');

        // Otros ingresos
        $otrosIngresos = MovimientoCaja::where('concepto', '!=', 'Apertura de caja')
            ->where('concepto', '!=', 'Venta de productos')
            ->where('tipo', '!=', 'egreso')
            ->whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])
            ->sum('monto');

        // Utilidad
        $utilidadBruta = $totalVentas + $otrosIngresos - $costoTotal;
        $utilidadNeta = $utilidadBruta - $egresos;

        // Servicios
        $servicios = ServicioProceso::whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])->get();
        $serviciosCompletados = $servicios->whereIn('estado', ['completado', 'cobrado'])->count();

        // Facturas
        $facturas = Factura::whereBetween('created_at', [$this->fechaInicio, $this->fechaFin])->count();

        // Productos vendidos
        $productosVendidos = $detalles->sum('cantidad');

        // Totales por forma de pago - usando la tabla pagos
        $ventaIds = $ventas->pluck('id');
        $pagos = Pago::whereIn('venta_id', $ventaIds)->get();
        $pagosPorMetodo = $pagos->groupBy('metodo');
        $totalEfectivo = $pagosPorMetodo->get('efectivo', collect())->sum('monto');
        $totalTransferencia = $pagosPorMetodo->get('transferencia', collect())->sum('monto');

        return [
            ['Período', $this->fechaInicio->format('d/m/Y') . ' - ' . $this->fechaFin->format('d/m/Y')],
            ['', ''],
            ['INGRESOS', ''],
            ['Total Ventas', 'Gs. ' . number_format($totalVentas, 0, ',', '.')],
            ['Cantidad de Ventas', $cantidadVentas],
            ['Otros Ingresos', 'Gs. ' . number_format($otrosIngresos, 0, ',', '.')],
            ['', ''],
            ['FORMAS DE PAGO', ''],
            ['Efectivo', 'Gs. ' . number_format($totalEfectivo, 0, ',', '.')],
            ['Transferencia', 'Gs. ' . number_format($totalTransferencia, 0, ',', '.')],
            ['', ''],
            ['COSTOS Y GASTOS', ''],
            ['Costo de Productos', 'Gs. ' . number_format($costoTotal, 0, ',', '.')],
            ['Total Egresos', 'Gs. ' . number_format($egresos, 0, ',', '.')],
            ['', ''],
            ['UTILIDAD', ''],
            ['Utilidad Bruta', 'Gs. ' . number_format($utilidadBruta, 0, ',', '.')],
            ['Utilidad Neta', 'Gs. ' . number_format($utilidadNeta, 0, ',', '.')],
            ['', ''],
            ['OPERACIONES', ''],
            ['Productos Vendidos (unidades)', $productosVendidos],
            ['Servicios Atendidos', $servicios->count()],
            ['Servicios Completados/Cobrados', $serviciosCompletados],
            ['Facturas Emitidas', $facturas],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            3 => ['font' => ['bold' => true]],  // INGRESOS
            8 => ['font' => ['bold' => true]],  // FORMAS DE PAGO
            12 => ['font' => ['bold' => true]], // COSTOS Y GASTOS
            16 => ['font' => ['bold' => true]], // UTILIDAD
            20 => ['font' => ['bold' => true]], // OPERACIONES
        ];
    }
}
