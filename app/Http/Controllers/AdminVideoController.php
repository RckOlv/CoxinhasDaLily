<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminVideoController extends Controller
{
    public function index()
    {
        $videos = Video::orderBy('sort_order')->orderBy('id', 'desc')->get();
        return inertia('Admin/VideoList', compact('videos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'videos' => 'required|array|max:10',
            'videos.*' => 'file|mimes:mp4,webm,mov|max:51200',
            'titles' => 'nullable|array',
            'titles.*' => 'nullable|string|max:255',
        ]);

        foreach ($request->file('videos') as $index => $file) {
            $path = $file->store('videos', 'public');
            $title = $request->input("titles.{$index}", pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

            Video::create([
                'video_path' => $path,
                'title' => $title,
            ]);
        }

        return back()->with('success', 'Videos subidos correctamente.');
    }

    public function update(Request $request, Video $video)
    {
        $video->update($request->validate([
            'title' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]));

        return back()->with('success', 'Video actualizado.');
    }

    public function destroy(Video $video)
    {
        Storage::disk('public')->delete($video->video_path);
        $video->delete();

        return back()->with('success', 'Video eliminado.');
    }
}
