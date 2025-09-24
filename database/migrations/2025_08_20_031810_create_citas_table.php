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
            $table->string("descripcion");            
            $table->foreignId("id_medico")->constrained("medicos")->onDelete("cascade");
            $table->foreignId("id_paciente")->constrained("pacientes")->onDelete("cascade");
            $table->date("fecha");
            $table->time("hora_inicio");           
            $table->enum("estado", ["Pendiente", "Confirmada", "Cancelada"]);
            $table->timestamps();
        }); 
    }

  

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
