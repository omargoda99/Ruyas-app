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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('ad_title'); // Title of the ad
            $table->text('ad_description'); // Description of the ad
            $table->string('ad_image_path')->nullable(); // Image path for the ad
            $table->string('link')->nullable(); // URL to redirect when clicked
            $table->dateTime('start_date'); // When the ad should start showing
            $table->dateTime('end_date'); // When the ad should stop showing
            $table->enum('status', ['active', 'expired'])->default('active'); // Status of the ad
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
