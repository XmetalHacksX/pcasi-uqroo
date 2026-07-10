<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();

            // FK explícita con constrained hacia users
            $table->foreignId('reporter_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('ticket_group', ['SGC', 'GENERO', 'INFRAESTRUCTURA']);

            // FK hacia statuses — asegúrate de que esa tabla exista antes
            $table->foreignId('status_id')
                ->constrained('statuses');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
