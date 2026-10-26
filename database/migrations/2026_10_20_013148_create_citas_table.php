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
        Schema::create('citas', function (Blueprint $table) {
        $table->id();
        $table->string('codigo_cita', 30)->unique();
        $table->foreignId('paciente_id')->constrained('pacientes');
        $table->foreignId('medico_id')->constrained('medicos');
        $table->foreignId('especialidad_id')->constrained('especialidades');
        $table->date('fecha_cita');
        $table->time('hora_inicio');
        $table->time('hora_fin');
        $table->text('motivo')->nullable();
        $table->text('motivo_cancelacion')->nullable();
        $table->string('estado', 30)->default('Programada');
        $table->timestamps();

        // Índices de rendimiento indicados en el script original
        $table->index(['medico_id', 'fecha_cita'], 'idx_citas_medico_fecha');
        $table->index('paciente_id', 'idx_citas_paciente');
    });

    // Restricción CHECK para los estados de la cita
    DB::statement("ALTER TABLE citas ADD CONSTRAINT chk_estado_cita CHECK (estado IN ('Programada', 'Confirmada', 'Atendida', 'Cancelada', 'Inasistencia'));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
