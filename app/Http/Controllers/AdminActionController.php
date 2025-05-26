<?php

namespace App\Http\Controllers;

use App\Models\AdminAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Dream;

class AdminActionController extends Controller
{
    public function logAction(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_uuid'    => 'required|uuid|exists:admins,uuid',
            'action_type'   => 'required|in:ban_user,delete_dream,delete_chat,edit_subscription,other',
            'target_uuid'   => 'required|uuid',
            'target_type'   => 'required|in:' . implode(',', [User::class, Dream::class]),
            'details'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get actual admin ID using UUID
        $adminId = \App\Models\Admin::where('uuid', $request->admin_uuid)->value('id');
        $targetId = $request->target_type::where('uuid', $request->target_uuid)->value('id');

        if (!$targetId) {
            return response()->json(['error' => 'Target not found.'], 404);
        }

        $adminAction = AdminAction::create([
            'admin_id'    => $adminId,
            'action_type' => $request->action_type,
            'target_id'   => $targetId,
            'target_type' => $request->target_type,
            'details'     => $request->details,
            'performed_at' => now(),
        ]);

        return response()->json($adminAction, 201);
    }

    public function index()
    {
        return response()->json(AdminAction::with('admin')->get());
    }

    public function getActionsByTarget($targetType, $targetUuid)
    {
        $targetClass = 'App\\Models\\' . ucfirst(strtolower($targetType));

        if (!class_exists($targetClass)) {
            return response()->json(['error' => 'Invalid target type'], 400);
        }

        $targetId = $targetClass::where('uuid', $targetUuid)->value('id');

        if (!$targetId) {
            return response()->json(['error' => 'Target not found.'], 404);
        }

        $actions = AdminAction::where('target_type', $targetClass)
            ->where('target_id', $targetId)
            ->get();

        return response()->json($actions);
    }

    public function delete($uuid)
    {
        $adminAction = AdminAction::where('uuid', $uuid)->first();

        if (!$adminAction) {
            return response()->json(['error' => 'Admin action not found.'], 404);
        }

        $adminAction->delete();

        return response()->json(['message' => 'Admin action deleted successfully.']);
    }
}
