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
        Schema::create('complains', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for each complain
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('interpreter_id')->constrained()->onDelete('cascade'); // Foreign key to interpreters table
            $table->text('complain_text'); // Complain text
            $table->enum('status', ['pending', 'resolved', 'closed'])->default('pending'); // Complain status
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
