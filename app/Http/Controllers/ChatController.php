<?php

namespace App\Http\Controllers;

use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\ChMessage;
use App\Models\Conversation;
use App\Models\Interpreter;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    //
    public function startConversation(Request $request)
    {
        $User = User::findOrFail($request->user_id);
        $interpreter = Interpreter::findOrFail($request->interpreter_id);

        // Create a new conversation
        $conversation = Conversation::create([
            'user_id' => $User->id,
            'interpreter_id' => $interpreter->id,
        ]);

        return response()->json(['conversation' => $conversation]);
    }
      // Send a message in a conversation
      public function sendMessage(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'from_id' => 'required|exists:users,id|exists:interpreters,id', // Sender ID (User or Interpreter)
            'to_id' => 'required|exists:users,id|exists:interpreters,id',   // Receiver ID (User or Interpreter)
            'message_body' => 'required|string',
            'attachment' => 'nullable|string',  // Optional attachment URL/path
            'sender_type' => 'required|string|in:User,Interpreter', // Sender type
            'receiver_type' => 'required|string|in:User,Interpreter', // Receiver type
        ]);

        // Dynamically determine sender and receiver types
        $senderType = $request->sender_type === 'User' ? 'User' : 'Interpreter';
        $receiverType = $request->receiver_type === 'User' ? 'User' : 'Interpreter';

        // Create the message
        $message = ChMessage::create([
            'conversation_id' => $request->conversation_id,
            'from_id' => $request->from_id,
            'to_id' => $request->to_id,
            'body' => $request->message_body,
            'attachment' => $request->attachment,
            'seen' => false,  // Default to false when sent
            'sender_type' => $senderType,  // Set sender type based on input
            'receiver_type' => $receiverType,  // Set receiver type based on input
        ]);

        // Broadcast the new message in real-time
        broadcast(new NewMessage($message));

        return response()->json(['message' => $message]);
    }


     /**
     * Get all messages in a conversation.
     */
    public function getMessages(Request $request)
    {
        // Fetch the conversation and its messages
        $conversationId = $request->input('conversation_id');
        $conversation = Conversation::findOrFail($conversationId);
        $messages = $conversation->messages;

        return response()->json(['messages' => $messages]);
    }

}
