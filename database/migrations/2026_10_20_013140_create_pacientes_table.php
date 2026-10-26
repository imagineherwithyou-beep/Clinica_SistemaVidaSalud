<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('set null');
        $table->string('codigo_paciente', 20)->unique();
        $table->string('ci', 30)->unique();
        $table->string('nombres', 100);
        $table->string('apellidos', 100);
        $table->date('fecha_nacimiento');
        $table->char('sexo', 1)->nullable();
        $table->string('telefono', 20);
        $table->string('correo', 100)->nullable();
        $table->text('direccion')->nullable();
        $table->string('tipo_sangre', 5)->nullable();
        $table->text('alergias')->nullable();
        $table->text('enfermedades_cronicas')->nullable();
        $table->string('contacto_emergencia_nombre', 100)->nullable();
        $table->string('contacto_emergencia_telefono', 20)->nullable();
        $table->boolean('estado')->default(true);
        $table->timestamps();

        $table->index('ci', 'idx_pacientes_ci');
    });

    // Restricción CHECK nativa para el sexo
    DB::statement("ALTER TABLE pacientes ADD CONSTRAINT pacientes_sexo_check CHECK (sexo IN ('M', 'F', 'O'));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
