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
       Schema::create('interpreter_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id')->unique(); // Each user can only request once
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->integer('age');
            $table->enum('gender', ['male', 'female']);
            $table->integer('years_of_experience')->default(0);
            $table->unsignedTinyInteger('memorized_quran_parts')->default(0);
            $table->json('languages')->nullable();
            $table->string('nationality');
            $table->string('pervious_work')->nullable();
            $table->string('country');
            $table->string('city');
            $table->enum('status', ['accebted', 'pending', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interpreter_requests');
    }
};
