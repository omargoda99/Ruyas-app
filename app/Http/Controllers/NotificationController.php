<?php

namespace App\Http\Controllers;

use App\Models\Notification as NotificationModel;
use App\Models\User;
use App\Notifications\SendAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Store the notification and send it to all users
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'link' => 'required|url',
            'link_type' => 'required|in:external,internal',
            'img_path' => 'nullable|image|max:2048',
        ]);

        $imgPath = null;
        if ($request->hasFile('img_path')) {
            $imgPath = $request->file('img_path')->store('notifications', 'public');
        }

        $notification = NotificationModel::create([
            'title' => $request->title,
            'description' => $request->description,
            'link' => $request->link,
            'link_type' => $request->link_type,
            'img_path' => $imgPath,
        ]);

        $this->sendNotificationToAllUsers($notification);

        return response()->json([
            'message' => 'Notification sent successfully!'
        ], 200);
    }

    public function sendNotificationToAllUsers(NotificationModel $notification)
    {
        $users = User::all();

        foreach ($users as $user) {
            $user->notify(new SendAdminNotification(
                $notification->title,
                $notification->description,
                $notification->link,
                $notification->link_type
            ));
        }
    }

    public function getUnreadNotifications()
    {
        $user = Auth::user();
        $unreadNotifications = $user->notifications()->whereNull('read_at')->get();

        return response()->json([
            'unread_notifications' => $unreadNotifications
        ], 200);
    }

    public function getReadNotifications()
    {
        $user = Auth::user();
        $readNotifications = $user->notifications()->whereNotNull('read_at')->get();

        return response()->json([
            'read_notifications' => $readNotifications
        ], 200);
    }

    public function markAsRead($notificationId)
    {
        $notification = NotificationModel::findOrFail($notificationId);
        $notification->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Notification marked as read'
        ], 200);
    }
}
