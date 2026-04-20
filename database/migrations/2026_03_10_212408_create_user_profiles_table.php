<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')       // ✅ FK real hacia users
                ->cascadeOnDelete();          // Si se borra el user, se borra su perfil
            $table->string('phone')->nullable();
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('sex')->nullable();
            $table->string('vulnerable_group')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
