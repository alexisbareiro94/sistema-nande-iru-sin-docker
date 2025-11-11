<?php


//implementar esto cuando se pueda usar reverb
namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportStockJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    public $productos;
    public function __construct($productos)
    {
        $this->productos = $productos;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
