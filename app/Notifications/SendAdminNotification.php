<?php

// app/Notifications/SendAdminNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SendAdminNotification extends Notification
{
    use Queueable;

    public $title;
    public $description;
    public $link;
    public $link_type;

    // Constructor to accept the notification data
    public function __construct($title, $description, $link, $link_type)
    {
        $this->title = $title;
        $this->description = $description;
        $this->link = $link;
        $this->link_type = $link_type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Store the notification in the database
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'link' => $this->link,
            'link_type' => $this->link_type,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // Additional data can be added here if necessary
        ];
    }
}
