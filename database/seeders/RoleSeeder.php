<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;          
class RoleSeeder extends Seeder 
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nombre_rol' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_rol' => 'Recepcionista', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_rol' => 'Medico', 'created_at' => now(), 'updated_at' => now()],
            ['nombre_rol' => 'Paciente', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
