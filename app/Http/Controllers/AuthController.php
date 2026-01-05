<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        crear_caja();
    }

    public function index()
    {
        return view('home.index');
    }

    public function login_view()
    {
        return view('Auth.login');
    }

    public function login(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|exists:users,email',
                'password' => 'required'
            ], [
                'email.required' => 'completar el campo email',
                'email.exists' => 'El email no esta registrado',
                'password.*' => 'completar el campo contraseña'
            ]);

            if (Auth::attempt($validate->validated())) {
                $user = Auth::user();

                if ($user->temp_password && !$user->temp_used) {
                    if ($user->expires_at < now()) {
                        return redirect()->back()->with('error', 'El usuario ha expirado');
                    }
                    return redirect()->route('auth.config.view');
                }

                $user->update([
                    'en_linea' => true,
                ]);
                if ($user->role == 'personal' || $user->role == 'caja') {
                    return redirect()->route('caja.index');
                }
                if ($user->role === 'cliente') {
                    session()->flush();
                }
                return redirect()->route('home');
            } else {
                return redirect()->back()->with('error', 'La contraseña es incorrecta');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', $e->getMessage());
        }
    }

    public function register_view()
    {
        return view('Auth.register');
    }

    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'empresa' => 'nullable|unique:users,empresa|string',
        ], [
            'name.required' => 'completar el campo nombre',
            'email.required' => 'completar el campo email',
            'email.unique' => 'El email ya esta registrado',
            'password.required' => 'completar el campo password',
            'password.min' => 'completar el campo password',
            'password.confirmed' => 'confirmar el campo password',
        ]);

        if ($validate->fails()) {
            return back()->with('error', $validate->messages()->first());
        }
        try {
            User::create($validate->validated());
            return redirect()->route('login')->with('success', 'Registro exitoso');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            $user->update([
                'ultima_conexion' => now(),
                'en_linea' => false,
            ]);
            Auth::logout();
            return redirect('/');
        } catch (\Exception) {
            return back()->with('error', 'Intente de vuelta');
        }
    }
}
