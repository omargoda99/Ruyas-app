<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Laravel\Ui\Presets\React;

class SubscriptionPlanController extends Controller
{
    // Get all subscription plans
    public function index()
    {
        $plans = SubscriptionPlan::select('uuid', 'name', 'description', 'price', 'features')->get();

        return response()->json([
            'status' => 'success',
            'data' => $plans,
        ]);
    }

    // Store a new plan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'features'    => 'required|array',
            'is_active'   => 'boolean',
        ]);

        $plan = SubscriptionPlan::create($validated);

        return response()->json([
            'status' => 'created',
            'data' => $plan,
        ], 201);
    }

    // Show a single plan by UUID
    public function show(Request $request)
    {
        $uuid = $request->input('uuid');
        $plan = SubscriptionPlan::where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $plan,
        ]);
    }

    // Update an existing plan by UUID
    public function update(Request $request)
    {
        $uuid = $request->input('uuid');
        $plan = SubscriptionPlan::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name'        => 'sometimes|string',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric',
            'features'    => 'sometimes|array',
            'is_active'   => 'boolean',
        ]);

        $plan->update($validated);

        return response()->json([
            'status' => 'success',
            'data' => $plan,
        ]);
    }

    // Delete a plan by UUID
    public function destroy(Request $request)
    {
        $uuid = $request->input('uuid');
        $plan = SubscriptionPlan::where('uuid', $uuid)->firstOrFail();
        $plan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription plan deleted successfully.',
        ]);
    }
}
