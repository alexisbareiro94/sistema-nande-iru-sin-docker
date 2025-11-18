<?php

namespace App\Http\Controllers;

use App\Models\Distribuidor;
use Illuminate\Http\Request;
use App\Models\User;

class ClienteDistController extends Controller
{
    public function index(){
        $tenantId = tenant_id();
        $clientes = User::withCount('compras')
            ->where('role', 'cliente')            
            ->where('tenant_id', $tenantId)
            ->orderByDesc('compras_count')            
            ->get()
            ->take(5);        
        $distribuidores = Distribuidor::whereNot('id', 1)
            ->get()            
            ->take(5);
        return view('gestios-usuarios.index', [
            'clientes' => $clientes,
            'distribuidores' => $distribuidores,
        ]);
    }

    public function show_cliente(string $id){
        try{
            $tenantId = tenant_id();
            $cliente = User::where('tenant_id', $tenantId)->findOrFail($id);
            return response()->json([
                'data' => $cliente
            ]);
        }catch(\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    //post
    public function desactive(string $id){
        try{
            $user = User::findOrFail($id)
                ->update([
                    'activo' => false,
                ]);

                return response()->json([
                    'message' => 'Usuario eliminado'
                ]);
        }catch(\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }
}
