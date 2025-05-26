<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Coupon::all());
    }

    // Store a new coupon
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|unique:coupons,code|max:255',
            'is_expired'  => 'boolean'
        ]);

        $coupon = Coupon::create($validated);

        return response()->json($coupon, 201);
    }

    // Show a specific coupon by UUID
    public function show($uuid)
    {
        $coupon = Coupon::where('uuid', $uuid)->firstOrFail();
        return response()->json($coupon);
    }

    // Update an existing coupon by UUID
    public function update(Request $request, $uuid)
    {
        $coupon = Coupon::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'code'        => 'sometimes|string|unique:coupons,code,' . $coupon->id,
            'is_expired'  => 'boolean'
        ]);

        $coupon->update($validated);

        return response()->json($coupon);
    }

    // Delete a coupon by UUID
    public function destroy($uuid)
    {
        $coupon = Coupon::where('uuid', $uuid)->firstOrFail();
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully.']);
    }
}
