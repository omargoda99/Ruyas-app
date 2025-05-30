<?php

namespace App\Events;

use App\Models\ChMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;



    /**
     * Create a new event instance.
     *
     * @param  ChMessage  $message
     * @return void
     */

    public function __construct(ChMessage $message)
    {
        $this->message = $message;

        // Debug log
        Log::info('NewMessage event created with message ID: ' . $message->id);
    }

    public function broadcastWith()
    {
        Log::info('NewMessage is broadcasting...', ['message_id' => $this->message->id]);

        return [
            'uuid' => $this->message->uuid,
            'message' => $this->message->body,
            'from_id' => $this->message->from_id,
            'to_id' => $this->message->to_id,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return [new PrivateChannel('ruyas_app')];
    }

    public function broadcastAs(){
        return 'message';
    }

}
