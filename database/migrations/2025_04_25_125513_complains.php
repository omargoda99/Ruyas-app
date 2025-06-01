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
        Schema::create('complains', function (Blueprint $table) {
        $table->id();
        $table->uuid('uuid')->unique();
        $table->uuid('user_uuid'); // store the user's UUID directly
        $table->text('complain_title');
        $table->text('complain_text');
        $table->enum('status', ['pending', 'resolved', 'closed'])->default('pending');
        $table->timestamps();
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
