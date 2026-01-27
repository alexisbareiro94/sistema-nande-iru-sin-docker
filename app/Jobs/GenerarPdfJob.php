<?php

namespace App\Jobs;

use App\Events\NotificacionEvent;
use App\Events\PdfGeneradoEvent;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerarPdfJob
{


    public $ventas;
    public $ingresos;
    public $egresos;
    public $userId;
    public $tenantId;
    public $path;

    public function __construct($userId, $ventas, $ingresos = null, $egresos = null, $tenantId = null, $path = null)
    {
        $this->ventas = $ventas;
        $this->ingresos = $ingresos;
        $this->egresos = $egresos;
        $this->userId = $userId;
        $this->tenantId = $tenantId;
        $this->path = $path ?: public_path("reports/report_" . time() . ".pdf");
    }


    public function handle(): void
    {
        try {
            if ($this->ingresos !== null && $this->egresos !== null) {
                $pdf = Pdf::loadView('pdf.reporte_movimientos', [
                    'ventas' => $this->ventas,
                    'ingresos' => $this->ingresos,
                    'egresos' => $this->egresos,
                ]);
            } else {
                $pdf = Pdf::loadView('pdf.ventas', [
                    'ventas' => $this->ventas
                ]);
            }

            $pdf->save($this->path);


        } catch (\Exception $e) {
            \Log::error("Error generating PDF: " . $e->getMessage());
            throw $e;
        }
    }
}
