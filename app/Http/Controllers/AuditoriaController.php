<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auditoria;
use App\Models\User;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Auditoria::with('user')->orderByDesc('id');

        // Filtro por usuario
        if ($request->filled('usuario')) {
            $query->where('created_by', $request->usuario);
        }

        // Filtro por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        // Filtro por acción
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        // Filtro por fecha desde
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        // Filtro por fecha hasta
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        // Búsqueda por descripción
        if ($request->filled('buscar')) {
            $query->where('descripcion', 'like', '%' . $request->buscar . '%');
        }

        // Obtener usuarios para el filtro
        $usuarios = User::select('id', 'name')
            ->whereIn('id', Auditoria::select('created_by')->distinct())
            ->get();

        // Obtener módulos únicos
        $modulos = Auditoria::select('modulo')
            ->whereNotNull('modulo')
            ->distinct()
            ->pluck('modulo');

        // Obtener acciones únicas
        $acciones = Auditoria::select('accion')
            ->distinct()
            ->pluck('accion');

        return view('usuarios.all-auditorias', [
            'auditorias' => $query->paginate(20)->withQueryString(),
            'usuarios' => $usuarios,
            'modulos' => $modulos,
            'acciones' => $acciones,
            'filtros' => $request->only(['usuario', 'modulo', 'accion', 'desde', 'hasta', 'buscar']),
        ]);
    }
}
