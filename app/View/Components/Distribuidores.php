<?php

namespace App\View\Components;

use App\Models\Distribuidor;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Distribuidores extends Component
{
    /**
     * Create a new component instance.
     */
    public $distribuidores;
    public function __construct()
    {
        $this->distribuidores = Distribuidor::all();        
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.distribuidores', [
            "distribuidores" => $this->distribuidores,
        ]);
    }
}
