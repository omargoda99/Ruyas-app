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
        Schema::create('admin_actions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade'); // Foreign key to admin
            $table->enum('action_type', ['ban_user', 'delete_dream', 'delete_chat', 'edit_subscription', 'other']); // Action type
            $table->morphs('target'); // Polymorphic relationship columns (target_id, target_type)
            $table->text('details')->nullable(); // Optional details
            $table->timestamp('performed_at')->useCurrent(); // Timestamp for the action
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_actions');
    }
};
