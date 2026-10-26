<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UsuarioRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CU03 – Gestionar usuarios, roles y permisos de acceso.
 * Solo accesible para el rol Administrador (ver routes/web.php).
 * Los usuarios no se eliminan: se desactivan.
 */
class UsuarioController extends Controller
{
    // Pasos 1 y 2: lista de usuarios con su rol
    public function index(): View
    {
        $usuarios = User::with('rol')->orderBy('nombre_usuario')->paginate(10);
        $total = User::count();
        $activos = User::where('estado', true)->count();

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'estadisticas' => [
                ['etiqueta' => 'Usuarios registrados', 'valor' => $total, 'icono' => 'users', 'tono' => 'neutral'],
                ['etiqueta' => 'Cuentas activas', 'valor' => $activos, 'icono' => 'user-check', 'tono' => 'neutral'],
                ['etiqueta' => 'Cuentas inactivas', 'valor' => $total - $activos, 'icono' => 'user-x', 'tono' => 'neutral'],
            ],
        ]);
    }

    // Paso 3: acción "Crear"
    public function create(): View
    {
        return view('admin.usuarios.form', [
            'usuario' => new User(),
            'roles' => Role::opciones(),
        ]);
    }

    // Pasos 4 y 5: validar y guardar
    public function store(UsuarioRequest $request): RedirectResponse
    {
        User::create($request->validated() + ['estado' => true]);

        return redirect()->route('admin.usuarios.index')->with('status', 'Usuario registrado.');
    }

    // Paso 3: acción "Editar rol" (y datos de la cuenta)
    public function edit(User $usuario): View
    {
        return view('admin.usuarios.form', [
            'usuario' => $usuario,
            'roles' => Role::opciones(),
        ]);
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $datos = $request->validated();

        // El administrador no puede quitarse a sí mismo el rol (evita quedarse sin acceso)
        if ($usuario->is($request->user())) {
            $datos['rol_id'] = $usuario->rol_id;
        }

        // Contraseña vacía = conservar la actual
        if (empty($datos['password'])) {
            unset($datos['password']);
        }

        $usuario->update($datos);

        return redirect()->route('admin.usuarios.index')->with('status', 'Cambios guardados.');
    }

    // Paso 3: acción "Desactivar" (también reactiva)
    public function cambiarEstado(Request $request, User $usuario): RedirectResponse
    {
        if ($usuario->is($request->user())) {
            return back()->with('error', 'No puede desactivar su propia cuenta.');
        }

        $usuario->update(['estado' => ! $usuario->estado]);

        return back()->with('status', $usuario->estado ? 'Usuario activado.' : 'Usuario desactivado.');
    }
}
