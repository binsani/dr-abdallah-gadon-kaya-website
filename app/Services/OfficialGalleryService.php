<?php

namespace App\Services;

use App\Models\GalleryAlbum;
use App\Models\GalleryImage;
use App\Models\Video;

class OfficialGalleryService
{
    public function syncFromYouTube(): int
    {
        $album = GalleryAlbum::firstOrCreate(
            ['slug' => 'official-channel-highlights'],
            ['title' => 'Official Channel Highlights', 'description' => 'Selected images from the official Dr. Abdallah Usman Gadon Kaya video channel. Each image links to its original source.']
        );

        $count = 0;
        Video::whereNotNull('youtube_id')->whereNotNull('thumbnail_url')->latest('published_at')->limit(60)->get()->each(function (Video $video, int $index) use ($album, &$count) {
            $source = 'https://www.youtube.com/watch?v='.$video->youtube_id;
            GalleryImage::updateOrCreate(
                ['gallery_album_id' => $album->id, 'source_url' => $source],
                ['image_path' => '', 'external_url' => $video->thumbnail_url, 'alt_text' => $video->title, 'sort_order' => $index]
            );
            $count++;
        });

        return $count;
    }
}
