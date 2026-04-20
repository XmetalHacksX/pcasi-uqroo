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
        Schema::create('ticket_gender_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');

            // --- DATOS DEL DENUNCIADO (Quién es) ---
            $table->string('reported_person_name')->nullable();
            $table->string('reported_person_type')->nullable(); // Estudiante, Docente, Administrativo

            $table->string('reported_person_details')->nullable();
            // Flujo Administrativo
            $table->foreignId('department_id')->nullable()->constrained();
            $table->foreignId('subdepartment_id')->nullable()->constrained();

            // Flujo Académico
            $table->foreignId('academic_division_id')->nullable()->constrained();
            $table->foreignId('educational_program_id')->nullable()->constrained();

            // --- UBICACIÓN DEL HECHO (Dónde pasó físicamente) ---
            $table->foreignId('building_id')->nullable()->constrained();
            $table->foreignId('location_id')->nullable()->constrained();

            // --- DETALLES DE LA QUEJA (Qué pasó) ---
            $table->string('manifestation_type'); // Violencia, Acoso, etc.
            $table->longText('chronological_narrative');
            $table->longText('extended_narrative')->nullable();
            $table->boolean('has_evidence')->default(false);
            $table->longText('witnesses_details')->nullable();
            $table->boolean('needs_psychological_support')->default(false);
            $table->json('communicated_to')->nullable();
            $table->longText('communication_results')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_gender_details');
    }
};
