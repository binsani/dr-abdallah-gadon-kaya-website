<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\GalleryImage;
use App\Services\OfficialGalleryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(GalleryAlbum $album) { return view('admin.gallery.images', compact('album')); }

    public function store(Request $request, GalleryAlbum $album)
    {
        $data = $request->validate(['images.*'=>'image|max:8192','external_url'=>'nullable|url|max:1000','source_url'=>'nullable|url|max:1000','alt_text'=>'nullable|max:255']);
        foreach ($request->file('images', []) as $index => $file) {
            $album->images()->create(['image_path'=>$file->store('gallery','public'),'alt_text'=>$data['alt_text'] ?? $album->title,'sort_order'=>$album->images()->max('sort_order') + $index + 1]);
        }
        if (! empty($data['external_url'])) $album->images()->create(['image_path'=>'','external_url'=>$data['external_url'],'source_url'=>$data['source_url'] ?? null,'alt_text'=>$data['alt_text'] ?? $album->title,'sort_order'=>$album->images()->max('sort_order') + 1]);
        return back()->with('success','Gallery images added.');
    }

    public function update(Request $request, GalleryImage $image)
    {
        $image->update($request->validate(['alt_text'=>'nullable|max:255','source_url'=>'nullable|url|max:1000','sort_order'=>'required|integer|min:0']));
        return back()->with('success','Image updated.');
    }

    public function destroy(GalleryImage $image)
    {
        if ($image->image_path) Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success','Image deleted.');
    }

    public function sync(OfficialGalleryService $gallery) { return back()->with('success','Synchronized '.$gallery->syncFromYouTube().' official images.'); }
}
