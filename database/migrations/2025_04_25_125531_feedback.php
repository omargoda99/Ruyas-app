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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for each feedback
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('interpreter_id')->constrained()->onDelete('cascade'); // Foreign key to interpreters table
            $table->foreignId('interpretation_id')->constrained()->onDelete('cascade'); // Foreign key to interpretations table
            $table->foreignId('dream_id')->constrained()->onDelete('cascade'); // Foreign key to dreams table
            $table->text('feedback_text'); // Feedback text
            $table->integer('rating')->default(0); // Rating (1-5)
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
