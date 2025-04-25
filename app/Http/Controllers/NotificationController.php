<?php

// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\Notification as NotificationModel;  // Correct model to use
use App\Models\User;
use App\Notifications\SendAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Store the notification in the database and send it to all users
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'required|url',
            'link_type' => 'required|in:external,internal',
        ]);

        // Create a new notification record in the database using the NotificationModel
        $notification = NotificationModel::create([
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'link_type' => $request->link_type,
        ]);

        // Send the notification to all users
        $this->sendNotificationToAllUsers($notification);

        // Return success response
        return response()->json([
            'message' => 'Notification sent successfully!'
        ], 200);
    }

    // Send notification to all users
    public function sendNotificationToAllUsers(NotificationModel $notification)
    {
        $users = User::all();  // Get all users from the database

        foreach ($users as $user) {
            // Send the notification to the user by passing the whole Notification model
            $user->notify(new SendAdminNotification($notification->title, $notification->description, $notification->link, $notification->link_type));
        }
    }

    // Get unread notifications for the logged-in user
    public function getUnreadNotifications()
    {
        $user = Auth::user();

        // Retrieve unread notifications (where read_at is null)
        $unreadNotifications = $user->notifications()->whereNull('read_at')->get();

        return response()->json([
            'unread_notifications' => $unreadNotifications
        ], 200);
    }

    // Get read notifications for the logged-in user
    public function getReadNotifications()
    {
        $user = Auth::user();

        // Retrieve read notifications (where read_at is not null)
        $readNotifications = $user->notifications()->whereNotNull('read_at')->get();

        return response()->json([
            'read_notifications' => $readNotifications
        ], 200);
    }

    // Mark notification as read (API version)
    public function markAsRead($notificationId)
    {
        // Find the notification
        $notification = NotificationModel::findOrFail($notificationId);

        // Mark the notification as read by setting 'read_at' to current timestamp
        $notification->update(['read_at' => now()]);

        // Return success response as JSON
        return response()->json([
            'message' => 'Notification marked as read'
        ], 200);
    }
}
