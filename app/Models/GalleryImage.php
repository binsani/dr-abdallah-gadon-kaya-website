<?php
namespace App\Models;
class GalleryImage extends ContentModel { public function album(){return $this->belongsTo(GalleryAlbum::class,'gallery_album_id');} public function getDisplayUrlAttribute(){return $this->external_url ?: ($this->image_path ? \Illuminate\Support\Facades\Storage::url($this->image_path) : null);} }
