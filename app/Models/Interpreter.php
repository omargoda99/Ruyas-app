<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interpreter extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'password', 'name', 'age', 'gender', 'ip_address', 'country', 'region', 'city', 'postal_code', 'status'];

     // Relationship with certifications (one-to-many)
     public function certifications()
     {
         return $this->hasMany(Certification::class, 'interpreter_id');
     }
       // Define the relationship to messages sent by the interpreter (as the sender)
    public function sentMessages()
    {
        return $this->hasMany(ChMessage::class, 'from_id');
    }

    // Define the relationship to messages received by the interpreter (as the receiver)
    public function receivedMessages()
    {
        return $this->hasMany(ChMessage::class, 'to_id');
    }
     public function conversations()
    {
        return $this->hasMany(Conversation::class, 'interpreter_id');
    }
}
