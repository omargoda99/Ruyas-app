<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   // database/migrations/xxxx_xx_xx_create_users_table.php
public function up()
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password_hash');
        $table->integer('age')->nullable();
        $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
        $table->enum('gender', ['male', 'female']);
        $table->enum('employment_status', ['employed', 'unemployed'])->nullable();
        $table->string('image_url')->nullable();
        $table->string('ip_address')->nullable();
        $table->string('country')->nullable();
        $table->string('region')->nullable();
        $table->string('city')->nullable();
        $table->string('postal_code')->nullable();
        $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
        $table->timestamps();
    });
}



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
