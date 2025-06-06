<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        // return new Channel('ruyas_app'); // public channel
        return new PrivateChannel('ruyas_app'); // if private
    }

    public function broadcastWith()
    {
        return [
            'uuid' => $this->notification->uuid,
            'title' => $this->notification->title,
            'description' => $this->notification->description,
            'link' => $this->notification->link,
            'link_type' => $this->notification->link_type,
            'img_path' => $this->notification->img_path,
            'created_at' => $this->notification->created_at->toDateTimeString(),
        ];
    }

    public function broadcastAs()
    {
        return 'new.notification';
    }
}
