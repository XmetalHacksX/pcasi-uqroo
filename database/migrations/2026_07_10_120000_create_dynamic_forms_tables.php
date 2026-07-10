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
        // 1. Table for Dynamic Forms
        Schema::create('dynamic_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. SGC, GENERO, INFRAESTRUCTURA
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Table for Dynamic Form Steps
        Schema::create('dynamic_form_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_form_id')->constrained('dynamic_forms')->cascadeOnDelete();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 3. Table for Dynamic Form Fields
        Schema::create('dynamic_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_form_step_id')->constrained('dynamic_form_steps')->cascadeOnDelete();
            $table->string('name'); // e.g. classification, reported_person_name
            $table->string('label');
            $table->string('type'); // e.g. text, textarea, select, toggle, select_campus, select_building...
            $table->text('options')->nullable(); // JSON serialized options for selects
            $table->string('placeholder')->nullable();
            $table->string('helper_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Add extra_attributes CLOB/Text column to details tables for dynamic questions
        Schema::table('ticket_sgc_details', function (Blueprint $table) {
            $table->text('extra_attributes')->nullable();
        });

        Schema::table('ticket_gender_details', function (Blueprint $table) {
            $table->text('extra_attributes')->nullable();
        });

        Schema::table('ticket_infra_details', function (Blueprint $table) {
            $table->text('extra_attributes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_sgc_details', function (Blueprint $table) {
            $table->dropColumn('extra_attributes');
        });

        Schema::table('ticket_gender_details', function (Blueprint $table) {
            $table->dropColumn('extra_attributes');
        });

        Schema::table('ticket_infra_details', function (Blueprint $table) {
            $table->dropColumn('extra_attributes');
        });

        Schema::dropIfExists('dynamic_form_fields');
        Schema::dropIfExists('dynamic_form_steps');
        Schema::dropIfExists('dynamic_forms');
    }
};
