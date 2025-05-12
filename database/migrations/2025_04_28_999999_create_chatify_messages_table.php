<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatifyMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ch_messages', function (Blueprint $table) {
            $table->id();  // This creates an auto-incrementing primary key
            $table->foreignId('conversation_id')->constrained('conversations');
            $table->foreignId('from_id');
            $table->foreignId('to_id');
            $table->text('body')->nullable();
            $table->string('attachment')->nullable();
            $table->string('voice')->nullable();
            $table->boolean('seen')->default(false);
            $table->string('sender_type');
            $table->string('receiver_type');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ch_messages');
    }
}
