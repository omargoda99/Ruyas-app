<?php

namespace App\Http\Controllers;

use App\Models\AppGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AppGuideController extends Controller
{
    /**
     * Display a listing of the guides.
     */
    public function index()
    {
        $guides = AppGuide::orderBy('order', 'asc')->get();
        return response()->json($guides);
    }

    /**
     * Store a newly created guide.
     */
    public function store(Request $request)
    {
        $request->validate([
            'view_title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'required|integer',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $request->hasFile('image_path')
            ? $request->file('image_path')->store('guide_images', 'public')
            : null;

        $guide = AppGuide::create([
            'view_title' => $request->view_title,
            'description' => $request->description,
            'order' => $request->order,
            'image_path' => $imagePath,
        ]);

        return response()->json($guide, 201);
    }

    /**
     * Display the specified guide by UUID.
     */
    public function show($uuid)
    {
        $guide = AppGuide::where('uuid', $uuid)->firstOrFail();
        return response()->json($guide);
    }

    /**
     * Update the specified guide by UUID.
     */
    public function update(Request $request, $uuid)
    {
        $guide = AppGuide::where('uuid', $uuid)->firstOrFail();

        $oldImage = $guide->image_path;

        $request->validate([
            'view_title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'required|integer',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $guide->view_title = $request->view_title;
        $guide->description = $request->description;
        $guide->order = $request->order;

        if ($request->hasFile('image_path')) {
            $newImage = $request->file('image_path')->store('guide_images', 'public');
            if ($oldImage && File::exists(public_path('storage/' . $oldImage))) {
                File::delete(public_path('storage/' . $oldImage));
            }
            $guide->image_path = $newImage;
        }

        $guide->save();

        return response()->json($guide, 200);
    }

    /**
     * Remove the specified guide by UUID.
     */
    public function destroy($uuid)
    {
        $guide = AppGuide::where('uuid', $uuid)->firstOrFail();

        // Delete image if it exists
        if ($guide->image_path && File::exists(public_path('storage/' . $guide->image_path))) {
            File::delete(public_path('storage/' . $guide->image_path));
        }

        $guide->delete();

        return response()->json(['message' => 'Guide deleted successfully.']);
    }
}
