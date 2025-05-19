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
            // Foreign key to the users table
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

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
            $table->string('pervious_work');
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');

            $table->timestamps();
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
