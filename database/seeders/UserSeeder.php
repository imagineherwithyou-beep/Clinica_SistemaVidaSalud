<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Obtenemos los roles creados previamente
        $adminRol = Role::where('nombre_rol', 'Administrador')->first();
        $recepcionRol = Role::where('nombre_rol', 'Recepcionista')->first();
        $medicoRol = Role::where('nombre_rol', 'Medico')->first();
        $pacienteRol = Role::where('nombre_rol', 'Paciente')->first();

        // Creamos los usuarios de prueba
        User::create([
            'nombre_usuario' => 'admin_general',
            'email' => 'admin@vidasalud.com',
            'password' => Hash::make('password123'),
            'rol_id' => $adminRol->id,
            'estado' => true,
        ]);

        User::create([
            'nombre_usuario' => 'recepcion_turno1',
            'email' => 'recepcion@vidasalud.com',
            'password' => Hash::make('password123'),
            'rol_id' => $recepcionRol->id,
            'estado' => true,
        ]);

        User::create([
            'nombre_usuario' => 'dr_perez',
            'email' => 'medico@vidasalud.com',
            'password' => Hash::make('password123'),
            'rol_id' => $medicoRol->id,
            'estado' => true,
        ]);

        User::create([
            'nombre_usuario' => 'paciente_test',
            'email' => 'paciente@vidasalud.com',
            'password' => Hash::make('password123'),
            'rol_id' => $pacienteRol->id,
            'estado' => true,
        ]);
    }
}
