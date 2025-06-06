<?php

namespace App\Http\Controllers;

use App\Events\MessageEdited;
use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use App\Models\ChMessage;
use App\Models\Conversation;
use App\Models\Interpreter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Start a new conversation using UUIDs.
     */
    public function startConversation(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,uuid',
            'interpreter_id' => 'required|exists:interpreters,uuid',
        ]);

        $user = User::where('uuid', $validated['user_id'])->firstOrFail();
        $interpreter = Interpreter::where('uuid', $validated['interpreter_id'])->firstOrFail();

        // Check if a conversation already exists between these two
        $existing = Conversation::where('user_id', $user->id)
            ->where('interpreter_id', $interpreter->id)
            ->first();

        if ($existing) {
            return response()->json([
                'conversation_uuid' => $existing->uuid,
                'user_uuid' => $user->uuid,
                'interpreter_uuid' => $interpreter->uuid
            ]);
        }

        // Create a new conversation
        $conversation = Conversation::create([
            'user_id' => $user->id,
            'interpreter_id' => $interpreter->id,
        ]);

        return response()->json([
            'conversation_uuid' => $conversation->uuid,
            'user_uuid' => $user->uuid,
            'interpreter_uuid' => $interpreter->uuid
        ]);
    }


    /**
     * Send a message using UUIDs.
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required',
            'from_id' => 'required',
            'to_id' => 'required',
            'body' => 'nullable|string',
            'attachment' => 'nullable|file',
            'voice' => 'nullable|file',
            'sender_type' => 'required|string|in:User,Interpreter',
            'receiver_type' => 'required|string|in:User,Interpreter',
        ]);

        $conversation = Conversation::where('uuid', $validated['conversation_id'])->first();

        // Convert UUIDs to actual model IDs
        $fromModel = $validated['sender_type'] === 'User'
        ? User::where('uuid', $validated['from_id'])->first()
        : Interpreter::where('uuid', $validated['from_id'])->first();


        $toModel = $validated['receiver_type'] === 'User'
        ? User::where('uuid', $validated['to_id'])->first()
        : Interpreter::where('uuid', $validated['to_id'])->first();


        $voicePath = null;
        if ($request->hasFile('voice')) {
            $file = $request->file('voice');
            $voicePath = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/voices', $voicePath);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/attachments', $attachmentPath);
        }

        $message = ChMessage::create([
            'uuid' => (string) Str::uuid(),
            'conversation_id' => $conversation->uuid,
            'from_id' => $fromModel->uuid,
            'to_id' => $toModel->uuid,
            'body' => $validated['body'] ?? null,
            'attachment' => $attachmentPath,
            'voice' => $voicePath,
            'seen' => false,
            'sender_type' => $validated['sender_type'],
            'receiver_type' => $validated['receiver_type'],
        ]);

        broadcast(new NewMessage($message))->toOthers();
        return response()->json(['message' => $message],201);

    }

    /**
     * Get all messages in a conversation using UUID.
     */
    public function getMessages(Request $request)
    {
        $validated = $request->validate([
            'conversation_uuid' => 'required|exists:conversations,uuid',
        ]);

        $uuid = $request->input('conversation_uuid'); // Correct field

        $conversation = Conversation::where('uuid', $uuid)->first();
        $messages = $conversation->messages;

        return response()->json(['messages' => $messages]);
    }

    public function editMessage(Request $request)
    {
        $validated = $request->validate([
            'message_uuid' => 'required|exists:ch_messages,uuid',
            'new_body' => 'required|string|min:1',
        ]);

        $message = ChMessage::where('uuid', $validated['message_uuid'])->firstOrFail();

        // Store old body before update
        $message->old_body = $message->body;
        $message->body = $validated['new_body'];
        $message->edited = true;
        $message->save();

        broadcast(new MessageEdited($message))->toOthers();

        return response()->json(['message' => $message, 'status' => 'updated']);
    }

}
