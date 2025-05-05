<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    // Get all subscription plans
    public function index()
    {
        $plans = SubscriptionPlan::select('id', 'name', 'description', 'price', 'features')->get();

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

    // Show a single plan
    public function show($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $plan,
        ]);
    }

    // Update an existing plan
    public function update(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

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

    // Delete a plan
    public function destroy($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription plan deleted successfully.',
        ]);
    }
}
