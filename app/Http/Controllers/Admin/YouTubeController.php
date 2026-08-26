<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Services\YouTubeSyncService;
class YouTubeController extends Controller { public function sync(YouTubeSyncService $service){try{$count=$service->sync();return back()->with('success',"Synchronized {$count} videos.");}catch(\Throwable $e){return back()->withErrors(['youtube'=>$e->getMessage()]);}} }
