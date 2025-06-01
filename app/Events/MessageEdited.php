<?php

namespace App\Events;

use App\Models\ChMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class MessageEdited implements ShouldBroadcastNow
{
    use SerializesModels;

    public $message;

    public function __construct(ChMessage $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('ruyas_app')];
    }

    public function broadcastWith()
    {
        return [
            'uuid' => $this->message->uuid,
            'new_body' => $this->message->body,
            'edited' => true,
        ];
    }

    public function broadcastAs()
    {
        return 'message.edited';
    }
}
