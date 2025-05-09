<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChMessage extends Model
{
    use HasFactory;

    protected $table = 'ch_messages';

    // Define the fillable fields
    protected $fillable = ['from_id', 'to_id', 'body', 'attachment', 'seen', 'conversation_id', 'sender_type', 'receiver_type'];

    // Define the polymorphic relationship to sender (User or Interpreter)
    public function sender()
    {
        return $this->morphTo('sender');
    }

    // Define the polymorphic relationship to receiver (User or Interpreter)
    public function receiver()
    {
        return $this->morphTo('receiver');
    }

    // Define relationship to Conversation
    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
}
