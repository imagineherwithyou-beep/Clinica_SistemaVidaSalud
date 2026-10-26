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
       Schema::create('horarios_atencion', function (Blueprint $table) {
        $table->id();
        $table->foreignId('medico_id')->constrained('medicos')->onDelete('cascade');
        $table->smallInteger('dia_semana');
        $table->time('hora_inicio');
        $table->time('hora_fin');
        $table->integer('intervalo_minutos')->default(30);
        $table->timestamps();
    });

    // Inyección de la restricción CHECK de PostgreSQL
    DB::statement("ALTER TABLE horarios_atencion ADD CONSTRAINT chk_horas CHECK (hora_fin > hora_inicio);");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_atencion');
    }
};
