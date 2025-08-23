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
            $table->string("nombre");
            $table->string("apellido");
            $table->string("documento")->unique();
            $table->string("telefono");
            $table->date("fecha_nacimiento");
            $table->string("rh")->nullable();
            $table->enum("sexo", ["F", "M"]);
            $table->string("nacionalidad");
            $table->string("correo")->unique();
            $table->string("clave");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
