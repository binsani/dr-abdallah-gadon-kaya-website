<?php

namespace App\Console\Commands;

use App\Services\OfficialGalleryService;
use Illuminate\Console\Command;

class SyncOfficialGallery extends Command
{
    protected $signature = 'gallery:sync-official';
    protected $description = 'Populate the gallery from verified official channel media';

    public function handle(OfficialGalleryService $gallery): int
    {
        $this->info('Synchronized '.$gallery->syncFromYouTube().' official gallery images.');
        return self::SUCCESS;
    }
}
