<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AdController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $ads = Ad::orderBy('start_date', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $ads->map(function ($ad) {
                return [
                    'uuid' => $ad->uuid,
                    'title' => $ad->ad_title,
                    'description' => $ad->ad_description,
                    'start_date' => $ad->start_date,
                    'end_date' => $ad->end_date,
                    'link' => $ad->link,
                    'status' => $ad->status,
                    'image_url' => $ad->ad_image_path ? asset('storage/' . $ad->ad_image_path) : null,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ad_title' => 'required|string|max:255',
            'ad_description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'ad_image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $imagePath = $request->hasFile('ad_image_path')
            ? $request->file('ad_image_path')->store('ads', 'public')
            : null;

        $ad = Ad::create([
            'ad_title' => $request->ad_title,
            'ad_description' => $request->ad_description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'ad_image_path' => $imagePath,
            'link' => $request->link,
            'status' => 'active',
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $ad,
        ], 201);
    }

    public function show(Request $request)
    {
        $uuid = $request->input('uuid');
        $ad = Ad::where('uuid', $uuid)->first();

        if (!$ad) {
            return response()->json([
                'status' => 'error',
                'message' => 'Advertisement not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'uuid' => $ad->uuid,
                'title' => $ad->ad_title,
                'description' => $ad->ad_description,
                'start_date' => $ad->start_date,
                'end_date' => $ad->end_date,
                'link' => $ad->link,
                'status' => $ad->status,
                'image_url' => $ad->ad_image_path ? asset('storage/' . $ad->ad_image_path) : null,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $uuid = $request->input('uuid');
        $ad = Ad::where('uuid', $uuid)->first();

        if (!$ad) {
            return response()->json(['status' => 'error', 'message' => 'Advertisement not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ad_title' => 'required|string|max:255',
            'ad_description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'ad_image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($request->hasFile('ad_image_path')) {
            if ($ad->ad_image_path && File::exists(public_path('storage/' . $ad->ad_image_path))) {
                File::delete(public_path('storage/' . $ad->ad_image_path));
            }
            $ad->ad_image_path = $request->file('ad_image_path')->store('ads', 'public');
        }

        $ad->update($request->only(['ad_title', 'ad_description', 'start_date', 'end_date', 'link']));

        return response()->json(['status' => 'success', 'data' => $ad]);
    }

    public function destroy(Request $request)
    {
        $uuid = $request->input('uuid');
        $ad = Ad::where('uuid', $uuid)->first();

        if (!$ad) {
            return response()->json(['status' => 'error', 'message' => 'Advertisement not found'], 404);
        }

        if ($ad->ad_image_path && File::exists(public_path('storage/' . $ad->ad_image_path))) {
            File::delete(public_path('storage/' . $ad->ad_image_path));
        }

        $ad->delete();

        return response()->json(['status' => 'success', 'message' => 'Advertisement deleted successfully']);
    }
}
