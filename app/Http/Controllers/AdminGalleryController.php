<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminGalleryController extends Controller
{
    public function index()
    {
        $images = GalleryImage::orderBy('sort_order')->orderBy('id', 'desc')->get();
        return inertia('Admin/GalleryList', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        foreach ($request->file('images') as $file) {
            $path = $file->store('gallery', 'public');
            GalleryImage::create([
                'image_path' => $path,
                'alt' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            ]);
        }

        return back()->with('success', 'Fotos subidas correctamente.');
    }

    public function update(Request $request, GalleryImage $image)
    {
        $image->update($request->validate([
            'alt' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]));

        return back()->with('success', 'Imagen actualizada.');
    }

    public function destroy(GalleryImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Imagen eliminada.');
    }
}
