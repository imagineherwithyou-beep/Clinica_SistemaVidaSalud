<?php

use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\HorarioAtencionController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\PacienteController;
use App\Http\Middleware\SesionActiva;
use App\Http\Middleware\VerificarRol;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// CU01 – Iniciar sesión (solo visitantes sin sesión)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Rutas internas: exigen sesión válida y cuenta activa.
// Los permisos se definen por rol con VerificarRol::using(...).
Route::middleware(['auth', SesionActiva::class])->group(function () {

    // CU02 – Cerrar sesión
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // CU01 (paso 5) – panel de inicio; sus módulos dependen del rol (config/menu.php)
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Administrador – CU03 (los CU08 a CU12 y CU20 se agregan a este grupo)
    Route::middleware(VerificarRol::using(Role::ADMINISTRADOR))
        ->prefix('admin')->name('admin.')->group(function () {
            Route::resource('usuarios', UsuarioController::class)
                ->only(['index', 'create', 'store', 'edit', 'update'])
                ->parameters(['usuarios' => 'usuario']);

            Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])
                ->name('usuarios.estado');

            Route::resource('medicos', MedicoController::class)
                ->except(['destroy'])->parameters(['medicos' => 'medico']);
            Route::patch('medicos/{medico}/estado', [MedicoController::class, 'cambiarEstado'])
                ->name('medicos.estado');

            Route::resource('especialidades', EspecialidadController::class)
                ->except(['destroy'])->parameters(['especialidades' => 'especialidad']);
            Route::patch('especialidades/{especialidad}/estado', [EspecialidadController::class, 'cambiarEstado'])
                ->name('especialidades.estado');

            Route::resource('horarios', HorarioAtencionController::class)
                ->only(['index', 'store', 'destroy'])->parameters(['horarios' => 'horarioAtencion']);
        });

    // CU09 – La recepción puede consultar médicos activos; la gestión es exclusiva del administrador.
    Route::middleware(VerificarRol::using(Role::RECEPCIONISTA))
        ->prefix('recepcion')->name('recepcion.')->group(function () {
            Route::get('medicos', [MedicoController::class, 'index'])->name('medicos.index');
            Route::get('medicos/{medico}', [MedicoController::class, 'show'])->name('medicos.show');

            Route::resource('pacientes', PacienteController::class)->except(['destroy']);
            Route::patch('pacientes/{paciente}/estado', [PacienteController::class, 'cambiarEstado'])
                ->name('pacientes.estado');

            Route::get('disponibilidad', [DisponibilidadController::class, 'index'])->name('disponibilidad.index');
        });

    // Recepcionista, Médico y Paciente: sus grupos de rutas se agregan aquí con
    // VerificarRol::using(Rol::RECEPCIONISTA), (Rol::MEDICO) y (Rol::PACIENTE).
});
