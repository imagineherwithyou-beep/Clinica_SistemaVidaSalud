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
        Schema::create('detalles_citas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cita_id')->unique()->constrained('citas')->onDelete('cascade');
        $table->text('diagnostico');
        $table->text('receta_medica')->nullable();
        $table->text('observaciones')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_citas');
    }
};
