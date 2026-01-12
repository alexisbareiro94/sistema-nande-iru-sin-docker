<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class VehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    private function tenantExists($tenantId)
    {
        return DB::table('users')->where('id', $tenantId)->exists();
    }

    public function run(): void
    {
        $tenantId = 1;
        if (!$this->tenantExists($tenantId)) {
            throw new \Exception('Tenant with id ' . $tenantId . ' does not exist in users table.');
        }

        $vehiculos = [
            'Toyota' => ['Corolla', 'Vitz', 'Premio', 'Funcargo'],
            'Kia' => ['Sportage', 'Sorento', 'Optima', 'Picanto'],
            'Hyundai' => ['Accent', 'Elantra', 'Tucson', 'Creta'],
            'Honda' => ['Civic', 'City', 'Accord', 'CR-V'],
            'Chevrolet' => ['Camaro', 'Cruze', 'Malibu', 'Silverado'],
        ];

        if ($this->tenantExists($tenantId)) {
            $count = 20;
            foreach ($vehiculos as $marca => $modelos) {
                foreach ($modelos as $modelo) {
                    DB::table('vehiculos')->insert([
                        'patente' => 'NA' . $count++,
                        'tenant_id' => $tenantId,
                        'marca' => $marca,
                        'modelo' => $modelo,
                    ]);
                }
            }
        }
    }
}
