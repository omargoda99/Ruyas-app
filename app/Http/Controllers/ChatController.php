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
        // Validate the request
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'interpreter_id' => 'required|exists:interpreters,id',
        ]);

        // Proceed with creation after validation
        $user = User::findOrFail($validated['user_id']);
        $interpreter = Interpreter::findOrFail($validated['interpreter_id']);

        // Create a new conversation
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'interpreter_id' => $interpreter->id,
        ]);

        return response()->json(['conversation' => $conversation]);
    }

      // Send a message in a conversation
   public function sendMessage(Request $request)
    {
        // Validate incoming data
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id', // Exists in conversations table
            'from_id' => 'required|exists:users,id|exists:interpreters,id', // Exists in users or interpreters table
            'to_id' => 'required|exists:users,id|exists:interpreters,id', // Exists in users or interpreters table
            'body' => 'nullable|string', // Body is nullable, but if provided, must be a string
            'attachment' => 'nullable|file', // Attachment is nullable, must be a file if provided
            'voice' => 'nullable|file', // Voice is nullable, must be a valid audio file if provided
            'sender_type' => 'required|string|in:User,Interpreter', // Sender type must be either User or Interpreter
            'receiver_type' => 'required|string|in:User,Interpreter', // Receiver type must be either User or Interpreter
        ]);

        // Dynamically determine sender and receiver types
        $senderType = $request->sender_type === 'User' ? 'User' : 'Interpreter';
        $receiverType = $request->receiver_type === 'User' ? 'User' : 'Interpreter';

        $voicePath = null;
        if ($request->hasFile('voice')) {
            $file = $request->file('voice');
            $voicePath = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/voices', $voicePath);
        } else {
            return response()->json(['error' => 'No voice file uploaded'], 400);
        }

        // Create the message
        $message = ChMessage::create([
            'conversation_id' => $request->conversation_id,
            'from_id' => $request->from_id,
            'to_id' => $request->to_id,
            'body' => $request->body,  // message body (if exists)
            'attachment' => $request->attachment,
            'voice' => $voicePath, // Save voice file path
            'seen' => false,
            'sender_type' => $senderType,
            'receiver_type' => $receiverType,
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
