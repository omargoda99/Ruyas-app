<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all subscription plans with only the id, name, and price
        $plans = SubscriptionPlan::select('id', 'name','price')->get();

        // Return the subscription plans as a JSON response
        return response()->json($plans);
    }

    // Store a new subscription plan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'features'    => 'required|array',
            'is_active'   => 'boolean',
        ]);

        // Store as JSON
        $validated['features'] = json_encode($validated['features']);

        $plan = SubscriptionPlan::create($validated);

        return response()->json($plan, 201);
    }

    // Show one subscription plan
    public function show(Request $request)
    {
        $id = $request->input('id');
        $plan = SubscriptionPlan::findOrFail($id);
        return response()->json($plan);
    }

    // Update an existing plan
    public function update(Request $request)
    {
        $id = $request->input('id');
        $plan = SubscriptionPlan::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'features'    => 'sometimes|array',
            'is_active'   => 'boolean',
        ]);

        if (isset($validated['features'])) {
            $validated['features'] = json_encode($validated['features']);
        }

        $plan->update($validated);

        return response()->json($plan);
    }

    // Delete a plan
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $plan = SubscriptionPlan::findOrFail($id);
        $plan->delete();

        return response()->json(['message' => 'Subscription plan deleted successfully.']);
    }
}
