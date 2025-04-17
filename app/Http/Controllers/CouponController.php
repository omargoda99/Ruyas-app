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

    // Show a specific coupon
    public function show($id)
    {
        $coupon = Coupon::findOrFail($id);
        return response()->json($coupon);
    }

    // Update an existing coupon
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code'        => 'sometimes|string|unique:coupons,code,' . $coupon->id,
            'is_expired'  => 'boolean'
        ]);

        $coupon->update($validated);

        return response()->json($coupon);
    }

    // Delete a coupon
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted successfully.']);
    }
}
