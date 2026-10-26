<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // CU01 – paso 5: panel de inicio. Los módulos visibles dependen del rol (config/menu.php).
    public function __invoke(Request $request): View
    {
        $usuario = $request->user();

        return view('dashboard', [
            'usuario' => $usuario,
            'modulos' => $usuario->modulos(),
            'estadisticas' => $this->estadisticasClinicas(),
        ]);
    }

    private function estadisticasClinicas(): array
    {
        return [
            ['etiqueta' => 'Citas de hoy', 'valor' => Cita::whereDate('fecha_cita', today())->count(), 'icono' => 'calendar', 'tono' => 'neutral'],
            ['etiqueta' => 'Pacientes registrados', 'valor' => Paciente::count(), 'icono' => 'user', 'tono' => 'neutral'],
            ['etiqueta' => 'Personal activo', 'valor' => User::where('estado', true)->whereHas('rol', fn ($query) => $query->where('nombre_rol', '!=', Role::PACIENTE))->count(), 'icono' => 'users', 'tono' => 'neutral'],
            ['etiqueta' => 'Por confirmar', 'valor' => Cita::where('estado', 'Programada')->count(), 'icono' => 'clock', 'tono' => 'neutral'],
        ];
    }
}
