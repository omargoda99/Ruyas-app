<?php

namespace App\Http\Controllers;

use App\Models\AdminAction;
use Illuminate\Http\Request;

class AdminActionController extends Controller
{
    public function logAction(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'admin_id'    => 'required|exists:admins,id',
            'action_type' => 'required|in:ban_user,delete_dream,delete_chat,edit_subscription,other',
            'target_id'   => 'required|integer',
            'target_type' => 'required|in:' . implode(',', [User::class, Dream::class]), // Add other models as necessary
            'details'     => 'nullable|string',
        ]);

        // Create the admin action
        $adminAction = AdminAction::create([
            'admin_id'    => $validated['admin_id'],
            'action_type' => $validated['action_type'],
            'target_id'   => $validated['target_id'],
            'target_type' => $validated['target_type'],
            'details'     => $validated['details'],
            'performed_at' => now(), // Capture the timestamp
        ]);

        return response()->json($adminAction, 201);
    }

    /**
     * Get all admin actions.
     */
    public function index()
    {
        return response()->json(AdminAction::all());
    }

    /**
     * Get admin actions for a specific user or dream (target).
     */
    public function getActionsByTarget($targetType, $targetId)
    {
        $targetType = ucfirst(strtolower($targetType)); // Capitalize first letter
        $targetClass = 'App\\Models\\' . $targetType;

        if (!class_exists($targetClass)) {
            return response()->json(['error' => 'Invalid target type'], 400);
        }

        $actions = AdminAction::where('target_type', $targetClass)
            ->where('target_id', $targetId)
            ->get();

        return response()->json($actions);
    }

    /**
     * Delete an admin action.
     */
    public function delete($id)
    {
        $adminAction = AdminAction::findOrFail($id);
        $adminAction->delete();

        return response()->json(['message' => 'Admin action deleted successfully.']);
    }
}
