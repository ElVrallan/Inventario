<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']); // solo admins
    }

    // Mostrar usuarios pendientes
    public function lista()
    {
        $usuarios = User::all(); // Trae todos los usuarios
        return view('admin.usuarios_lista', compact('usuarios'));
    }



    // Aprobar usuario
    public function aprobar(User $user)
    {
        $user->rol = 'vendedor'; // o 'admin' si quieres
        $user->save();

        return redirect()->back()->with('success', 'Usuario aprobado correctamente.');
    }

    // Rechazar usuario (opcional)
    public function rechazar(User $user)
    {
        $user->delete(); // o cualquier otra acción
        return redirect()->back()->with('success', 'Usuario rechazado.');
    }

    public function cambiarRol(Request $request)
    {
        $roles = $request->input('roles', []);
        foreach ($roles as $id => $rol) {
            $usuario = \App\Models\User::find($id);
            if ($usuario && $usuario->rol !== $rol) {
                $usuario->rol = $rol;
                $usuario->save();
            }
        }
        
        // Si se envía una URL de redirección, redirigir allí
        if ($request->has('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Roles actualizados correctamente');
        }
        
        return back()->with('success', 'Roles actualizados correctamente');
    }


}
