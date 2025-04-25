<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('interpreters', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID for each interpreter
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('age');
            $table->enum('gender', ['male', 'female']);
            $table->integer('years_of_experience');
            $table->unsignedTinyInteger('memorized_quran_parts')
                  ->default(0)
                  ->check('memorized_quran_parts >= 0 AND memorized_quran_parts <= 31');
            $table->json('languages')->nullable();
            $table->string('nationality');
            $table->string('country');
            $table->string('city');
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
            $table->timestamps();

            // Add the foreign key columns first
            $table->unsignedBigInteger('certifications_id')->nullable(); // Foreign key for certifications
            $table->unsignedBigInteger('interpretations_id')->nullable(); // Foreign key for interpretations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interpreters');
    }
};
