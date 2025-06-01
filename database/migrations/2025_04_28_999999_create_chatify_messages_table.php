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
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('conversation_id');

            // Change these to uuid instead of foreignId()
            $table->uuid('from_id');
            $table->uuid('to_id');

            $table->text('body')->nullable();
            $table->string('attachment')->nullable();
            $table->string('voice')->nullable();
            $table->boolean('seen')->default(false);
            $table->string('sender_type');
            $table->string('receiver_type');
            $table->boolean('edited')->default(false);
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
