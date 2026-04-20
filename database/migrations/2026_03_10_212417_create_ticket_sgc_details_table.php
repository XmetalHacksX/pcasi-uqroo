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
        Schema::create('ticket_sgc_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();

            // ¡Este campo te faltaba en la BD!
            $table->string('reported_person_name')->nullable();

            // Para saber si seleccionó Dirección o División
            $table->string('area_type')->nullable();

            // Llaves foráneas a nuestros catálogos reales
            $table->foreignId('department_id')->nullable()->constrained();
            $table->foreignId('subdepartment_id')->nullable()->constrained();
            $table->foreignId('academic_division_id')->nullable()->constrained();

            $table->string('classification');
            $table->longText('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_sgc_details');
    }
};
