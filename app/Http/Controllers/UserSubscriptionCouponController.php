<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserSubscriptionCouponController extends Controller
{
    /**
     * Display a listing of all user subscriptions.
     */
    public function index()
    {
        $subscriptions = DB::table('user_subscription_coupon')
            ->join('users', 'user_subscription_coupon.user_id', '=', 'users.id')
            ->join('subscription_plans', 'user_subscription_coupon.plan_id', '=', 'subscription_plans.id')
            ->leftJoin('coupons', 'user_subscription_coupon.coupon_id', '=', 'coupons.id')
            ->select(
                'user_subscription_coupon.uuid',
                'user_subscription_coupon.user_id',
                'user_subscription_coupon.plan_id',
                'user_subscription_coupon.coupon_id',
                'user_subscription_coupon.starts_at',
                'user_subscription_coupon.ends_at',
                'user_subscription_coupon.is_active',
                'user_subscription_coupon.purchased_at',
                'users.name as user_name',
                'subscription_plans.name as plan_name',
                'coupons.code as coupon_code'
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $subscriptions,
        ]);
    }

    /**
     * Store a newly created user subscription.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'      => 'required|exists:users,id',
                'plan_id'      => 'required|exists:subscription_plans,id',
                'coupon_id'    => 'nullable|exists:coupons,id',
                'starts_at'    => 'required|date',
                'ends_at'      => 'nullable|date|after_or_equal:starts_at',
                'is_active'    => 'boolean',
                'purchased_at' => 'nullable|date',
            ]);

            $validated['purchased_at'] = $validated['purchased_at'] ?? now();

            // Generate UUID for the new subscription record
            $uuid = (string) \Illuminate\Support\Str::uuid();

            // Insert with UUID
            DB::table('user_subscription_coupon')->insert(
                array_merge($validated, ['uuid' => $uuid])
            );

            $subscription = DB::table('user_subscription_coupon')
                ->join('users', 'user_subscription_coupon.user_id', '=', 'users.id')
                ->join('subscription_plans', 'user_subscription_coupon.plan_id', '=', 'subscription_plans.id')
                ->leftJoin('coupons', 'user_subscription_coupon.coupon_id', '=', 'coupons.id')
                ->select(
                    'user_subscription_coupon.uuid',
                    'user_subscription_coupon.user_id',
                    'user_subscription_coupon.plan_id',
                    'user_subscription_coupon.coupon_id',
                    'user_subscription_coupon.starts_at',
                    'user_subscription_coupon.ends_at',
                    'user_subscription_coupon.is_active',
                    'user_subscription_coupon.purchased_at',
                    'users.name as user_name',
                    'subscription_plans.name as plan_name',
                    'coupons.code as coupon_code'
                )
                ->where('user_subscription_coupon.uuid', $uuid)
                ->first();

            return response()->json([
                'status' => 'created',
                'data' => $subscription,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display a single user subscription by UUID.
     */
    public function show($uuid)
    {
        $subscription = DB::table('user_subscription_coupon')
            ->join('users', 'user_subscription_coupon.user_id', '=', 'users.id')
            ->join('subscription_plans', 'user_subscription_coupon.plan_id', '=', 'subscription_plans.id')
            ->leftJoin('coupons', 'user_subscription_coupon.coupon_id', '=', 'coupons.id')
            ->select(
                'user_subscription_coupon.uuid',
                'user_subscription_coupon.user_id',
                'user_subscription_coupon.plan_id',
                'user_subscription_coupon.coupon_id',
                'user_subscription_coupon.starts_at',
                'user_subscription_coupon.ends_at',
                'user_subscription_coupon.is_active',
                'user_subscription_coupon.purchased_at',
                'users.name as user_name',
                'subscription_plans.name as plan_name',
                'coupons.code as coupon_code'
            )
            ->where('user_subscription_coupon.uuid', $uuid)
            ->first();

        if (!$subscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subscription not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $subscription,
        ]);
    }

    /**
     * Update an existing subscription by UUID.
     */
    public function update(Request $request, $uuid)
    {
        $subscription = DB::table('user_subscription_coupon')->where('uuid', $uuid)->first();

        if (!$subscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subscription not found.',
            ], 404);
        }

        try {
            $validated = $request->validate([
                'starts_at'    => 'sometimes|date',
                'ends_at'      => 'nullable|date|after_or_equal:starts_at',
                'coupon_id'    => 'nullable|exists:coupons,id',
                'is_active'    => 'boolean',
                'purchased_at' => 'nullable|date',
            ]);

            DB::table('user_subscription_coupon')->where('uuid', $uuid)->update($validated);

            $updated = DB::table('user_subscription_coupon')
                ->join('users', 'user_subscription_coupon.user_id', '=', 'users.id')
                ->join('subscription_plans', 'user_subscription_coupon.plan_id', '=', 'subscription_plans.id')
                ->leftJoin('coupons', 'user_subscription_coupon.coupon_id', '=', 'coupons.id')
                ->select(
                    'user_subscription_coupon.uuid',
                    'user_subscription_coupon.user_id',
                    'user_subscription_coupon.plan_id',
                    'user_subscription_coupon.coupon_id',
                    'user_subscription_coupon.starts_at',
                    'user_subscription_coupon.ends_at',
                    'user_subscription_coupon.is_active',
                    'user_subscription_coupon.purchased_at',
                    'users.name as user_name',
                    'subscription_plans.name as plan_name',
                    'coupons.code as coupon_code'
                )
                ->where('user_subscription_coupon.uuid', $uuid)
                ->first();

            return response()->json([
                'status' => 'success',
                'data' => $updated,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Delete a user subscription by UUID.
     */
    public function destroy($uuid)
    {
        $subscription = DB::table('user_subscription_coupon')->where('uuid', $uuid)->first();

        if (!$subscription) {
            return response()->json([
                'status' => 'error',
                'message' => 'Subscription not found.',
            ], 404);
        }

        DB::table('user_subscription_coupon')->where('uuid', $uuid)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Subscription deleted successfully.',
        ]);
    }
}
