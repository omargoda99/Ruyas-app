<?php

namespace App\Http\Controllers;

use App\Models\AppGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class AppGuideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
      // Display a listing of the guides
      public function index()
      {
          // Get all guides ordered by the 'order' field in ascending order
          $guides = AppGuide::orderBy('order', 'asc')->get();
          // Return the guides as a JSON response
          return response()->json($guides);
      }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    // Validate the incoming request
        $request->validate([
            'view_title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image validation
        ]);

        // Handle the image upload if present
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image_path')->store('guide_images', 'public'); // Store image in 'public' disk
        } else {
            $imagePath = null; // If no image is uploaded, set it to null
        }

        // Create the new AppGuide entry
        $guide = AppGuide::create([
            'view_title' => $request->view_title,
            'description' => $request->description,
            'order' => $request->order,
            'image_path' => $imagePath, // Store the image path in the database
        ]);

        return response()->json($guide, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $id = $request->input('id');
        $guide = AppGuide::findOrFail($id);

        if (!$guide) {
            return response()->json(['message' => 'guide view not found'], status: 404);
        }
        return response()->json($guide,200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppGuide $appGuide)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // Update the specified guide in the database
    public function update(Request $request)
    {
        $id = $request->input('id');
        $guide = AppGuide::find($id);

        if (!$guide) {
            return response()->json(['message' => 'guide view not found'], status: 404);
        }
        $old_image = $guide->image;

        $request->validate([
            'view_title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'required|integer',
            'image_path' => 'nullable|string',
        ]);

        $guide->view_title = $request->view_title;
        $guide->description = $request->description;
        $guide->order = $request->order ;

        if ($request->hasFile('image_path')){
            $image = $request->file('image_path')->store('public');
            File::delete($old_image);
            $guide->image = $image;
        }

        $guide->save();
        return response()->json($guide,201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        //
        $id = $request->input('id');
        $guide = AppGuide::find($id);

        if (!$guide) {
            return response()->json(['message' => 'Feature not found'], 404);
        }

        $guide->delete();

        return response()->json(['message' => 'guide view deleted'], 200);
    }
}
