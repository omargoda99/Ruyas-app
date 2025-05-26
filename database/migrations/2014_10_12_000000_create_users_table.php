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
        $table->uuid('uuid')->unique()->index();
        $table->string('name')->nullable();
        $table->string('email')->nullable()->unique(); // Nullable because phone can be used
        $table->string('phone')->nullable()->unique();  // Nullable because email can be used
        $table->string('password');
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
        $table->timestamp('last_activity_at')->nullable();
        $table->softDeletes(); // This adds the `deleted_at` column
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
