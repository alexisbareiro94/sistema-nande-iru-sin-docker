<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Distribuidor;
use App\Models\Marca;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    public function definition(): array
    {
        $nombres = [
            'producto' => ['cubierta 175/70R13', 'cubierta 185/65R15', 'traba volante', 'perfume', 'colgante'],
            'servicio' => ['cambio de neumático', 'cambio de llanta', 'alineacion', 'gomeria'],
        ];

        $tipo = $this->faker->boolean(90) ? 'producto' : 'servicio';
        $codigoAuto = $this->faker->boolean(20);

        $precioVenta = round($this->faker->numberBetween(70000, 425000), -3);

        $precioCompra = $tipo === 'producto'
            ? round($this->faker->numberBetween(10000, $precioVenta - 30000), -3)
            : null;

        return [
            'nombre' => $this->faker->randomElement($nombres[$tipo]),
            'tenant_id' => 1,
            'tipo' => $tipo,
            'codigo' => $codigoAuto ? null : $this->faker->unique()->regexify('[A-Z0-9]{8}'),
            'marca_id' => $tipo === 'producto' ? Marca::inRandomOrder()->value('id') : null,
            'categoria_id' => $tipo === 'producto' ? Categoria::inRandomOrder()->value('id') : null,
            'descripcion' => $this->faker->sentence(),
            'stock' => $tipo === 'producto' ? $this->faker->numberBetween(0, 12) : null,
            'stock_minimo' => $tipo === 'producto' ? $this->faker->numberBetween(0, 4) : null,
            'precio_venta' => $precioVenta,
            'precio_compra' => $precioCompra,
            'distribuidor_id' => $tipo === 'producto' ? Distribuidor::inRandomOrder()->value('id') : null,
            'imagen' => null,
        ];

    }
}
