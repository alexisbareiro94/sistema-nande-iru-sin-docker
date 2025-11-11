<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\User;

class PersonalExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $users;
    public function __construct()
    {
        try {
            $this->users = User::withCount('ventas')
                ->where('role', 'personal')->get();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Teléfono',
            'Correo',
            'Salario',
            'Ultima Conexión',
            'Ventas',
            'Activo',
            'Bloqueado',
            'Registrado',
            'Ultima Actualización'
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->telefono,
            $user->email,
            $user->salario,
            $user->ultima_conexion,
            $user->ventas_count,
            $user->activo == true ? 'Activo' : 'Inactivo',
            $user->is_blocked == true ? 'Bloqueado' : '',
            $user->created_at,
            $user->updated_at,
        ];
    }
}
