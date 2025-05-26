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
        Schema::create('app_guides', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('view_title'); // Name of the view or step
            $table->text('description'); // Explanation of the view or step
            $table->integer('order')->default(0); // Order in which the view appears
            $table->string('image_path')->nullable(); // Image associated with the guide (path or URL)
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_guides');
    }
};
