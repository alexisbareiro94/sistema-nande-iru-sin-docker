<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatoMecanico extends Model
{
    protected $table = "dato_mecanicos";
    protected $fillable = [
        'mecanico_id',
        'cliente_id',
        'datos',
    ];

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }
}
