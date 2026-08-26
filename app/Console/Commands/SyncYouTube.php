<?php
namespace App\Console\Commands;
use App\Services\YouTubeSyncService; use Illuminate\Console\Command;
class SyncYouTube extends Command { protected $signature='youtube:sync {--feed-file= : Import a previously downloaded official YouTube RSS feed}'; protected $description='Synchronize videos from the configured YouTube channel'; public function handle(YouTubeSyncService $sync){try{$file=$this->option('feed-file');if($file&&!is_file($file))throw new \RuntimeException('The supplied feed file does not exist.');$count=$file?$sync->syncFeedXml(file_get_contents($file)):$sync->sync();$this->info("Synchronized {$count} videos.");return self::SUCCESS;}catch(\Throwable $e){$this->error($e->getMessage());return self::FAILURE;}} }
