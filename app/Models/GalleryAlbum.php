<?php
namespace App\Models;
class GalleryAlbum extends ContentModel { public function images(){return $this->hasMany(GalleryImage::class)->orderBy('sort_order');} }
