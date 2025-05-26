<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_interpretations_table.php
    public function up()
    {
        Schema::create('interpretations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('dream_id')->constrained('dreams')->onDelete('cascade');
            $table->foreignId('interpreter_id')->constrained('interpreters')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interpretations');
    }
};
