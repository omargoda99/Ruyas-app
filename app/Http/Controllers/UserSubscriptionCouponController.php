<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserSubscriptionCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSubscriptionCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = DB::table('user_subscription_coupon')->get();
        return response()->json($subscriptions);
    }

    // Create a new subscription
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id',
            'plan_id'     => 'required|exists:subscription_plans,id',
            'coupon_id'   => 'nullable|exists:coupons,id',
            'starts_at'   => 'required|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
            'is_active'   => 'boolean',
            'purchased_at'=> 'nullable|date',
        ]);

        $validated['purchased_at'] = $validated['purchased_at'] ?? now();

        $id = DB::table('user_subscription_coupon')->insertGetId($validated);

        $subscription = DB::table('user_subscription_coupon')->find($id);

        return response()->json($subscription, 201);
    }

    // Show a single subscription
    public function show($id)
    {
        $subscription = DB::table('user_subscription_coupon')->find($id);

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found.'], 404);
        }

        return response()->json($subscription);
    }

    // Update subscription
    public function update(Request $request, $id)
    {
        $subscription = DB::table('user_subscription_coupon')->find($id);

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found.'], 404);
        }

        $validated = $request->validate([
            'starts_at'    => 'sometimes|date',
            'ends_at'      => 'nullable|date|after_or_equal:starts_at',
            'coupon_id'    => 'nullable|exists:coupons,id',
            'is_active'    => 'boolean',
            'purchased_at' => 'nullable|date',
        ]);

        DB::table('user_subscription_coupon')->where('id', $id)->update($validated);

        return response()->json(DB::table('user_subscription_coupon')->find($id));
    }

    // Delete a subscription
    public function destroy($id)
    {
        $subscription = DB::table('user_subscription_coupon')->find($id);

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found.'], 404);
        }

        DB::table('user_subscription_coupon')->delete($id);

        return response()->json(['message' => 'Subscription deleted successfully.']);
    }
}
