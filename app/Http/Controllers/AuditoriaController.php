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

        if ($request->filled('usuario')) {
            $query->where('created_by', $request->usuario);
        }
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }
        if ($request->filled('buscar')) {
            $query->where('descripcion', 'like', '%' . $request->buscar . '%');
        }

        $usuarios = User::select('id', 'name')
            ->whereIn('id', Auditoria::select('created_by')->distinct())
            ->get();

        $modulos = Auditoria::select('modulo')
            ->whereNotNull('modulo')
            ->distinct()
            ->pluck('modulo');

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
