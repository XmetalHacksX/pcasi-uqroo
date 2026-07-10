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
        Schema::table('ticket_sgc_details', function (Blueprint $table) {
            $table->string('user_type')->nullable()->after('ticket_id');
            $table->foreignId('educational_program_id')->nullable()->after('academic_division_id')->constrained('educational_programs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_sgc_details', function (Blueprint $table) {
            $table->dropForeign(['educational_program_id']);
            $table->dropColumn(['user_type', 'educational_program_id']);
        });
    }
};
