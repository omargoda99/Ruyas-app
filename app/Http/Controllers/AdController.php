<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class AdController extends Controller
{
    /**
     * Display a listing of the advertisements.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $ads = Ad::orderBy('start_date', 'asc')->get();

        return response()->json($ads->map(function ($ad) {
            return [
                'id' => $ad->id,
                'title' => $ad->ad_title,
                'description' => $ad->ad_description,
                'start_date' => $ad->start_date,
                'end_date' => $ad->end_date,
                'link' => $ad->link,
                'image_url' => asset('storage' . $ad->ad_image_path),
            ];
        }));
    }

    /**
     * Store a newly created advertisement in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'ad_title' => 'required|string|max:255',
            'ad_description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'ad_image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image validation
            'link' => 'nullable|url', // Validate the link
        ]);

        // Handle the image upload if present
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ads','public'); // Store image in 'public' disk
        } else {
            $imagePath = null; // If no image is uploaded, set it to null
        }

        // Create the new Ad entry
        $ad = Ad::create([
            'ad_title' => $request->ad_title,
            'ad_description' => $request->ad_description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'ad_image_path' => $imagePath, // Store the image path in the database
            'link' => $request->link,
            'status' => 'active', // Default status to active
        ]);

        return response()->json($ad, 201);
    }

    /**
     * Display the specified advertisement.
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $ad = Ad::find($id);

        if (!$ad) {
            return response()->json(['message' => 'Advertisement not found'], 404);
        }

        return response()->json($ad, 200);
    }

    /**
     * Update the specified advertisement in storage.
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $ad = Ad::find($id);

        if (!$ad) {
            return response()->json(['message' => 'Advertisement not found'], 404);
        }

        $old_image = $ad->image_path;

        // Validate the incoming request
        $request->validate([
            'ad_title' => 'required|string|max:255',
            'ad_description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'ad_image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'nullable|url',
        ]);

        // Update the Ad details
        $ad->ad_title = $request->ad_title;
        $ad->ad_description = $request->ad_description;
        $ad->start_date = $request->start_date;
        $ad->end_date = $request->end_date;
        $ad->link = $request->link;

        // Handle the image upload if present
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('ad_images', 'public');
            File::delete($old_image); // Delete the old image
            $ad->image_path = $image;
        }

        $ad->save();
        return response()->json($ad, 201);
    }

    /**
     * Remove the specified advertisement from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $ad = Ad::find($id);

        if (!$ad) {
            return response()->json(['message' => 'Advertisement not found'], 404);
        }

        // Delete the ad image from storage if exists
        if ($ad->image_path) {
            File::delete($ad->image_path);
        }

        $ad->delete();

        return response()->json(['message' => 'Advertisement deleted successfully'], 200);
    }
}
