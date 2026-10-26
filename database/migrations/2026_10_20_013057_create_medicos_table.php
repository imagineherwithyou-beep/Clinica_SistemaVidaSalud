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
        Schema::create('medicos', function (Blueprint $table) {
        $table->id();
        $table->string('registro_profesional', 50)->unique();
        $table->string('nombres', 100);
        $table->string('apellidos', 100);
        $table->string('telefono', 20);
        $table->string('correo', 100)->nullable();
        $table->foreignId('user_id')->nullable()->unique()->constrained('users')->onDelete('set null');
        $table->boolean('estado')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};
