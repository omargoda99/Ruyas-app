<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_create_interpreters_table.php
public function up()
{
    Schema::create('interpreters', function (Blueprint $table) {
        $table->id(); // Auto-incrementing ID for each interpreter
        $table->string('name'); // Interpreter name
        $table->string('email')->unique(); // Interpreter email (unique)
        $table->string('password'); // Interpreter password
        $table->integer('age')->nullable(); // Interpreter age
        $table->enum('gender', ['male', 'female']); // Interpreter gender
        $table->string('ip_address')->nullable(); // IP address
        $table->string('country')->nullable(); // Country
        $table->string('region')->nullable(); // Region
        $table->string('city')->nullable(); // City
        $table->string('postal_code')->nullable(); // Postal code
        $table->enum('status', ['active', 'inactive', 'banned'])->default('active'); // Interpreter status
        $table->timestamps(); // created_at and updated_at timestamps

       
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
