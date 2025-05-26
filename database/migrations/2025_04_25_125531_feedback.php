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
            $table->uuid('uuid')->unique();
            $table->foreignId('interpretation_id')->constrained()->onDelete('cascade'); // Foreign key to interpretations table
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
