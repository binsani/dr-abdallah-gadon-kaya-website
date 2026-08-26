<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\{Article,ContactMessage,Event,Video};
class DashboardController extends Controller { public function __invoke(){return view('admin.dashboard',['stats'=>['Videos'=>Video::count(),'Published articles'=>Article::where('status','published')->count(),'Unread messages'=>ContactMessage::where('is_read',false)->count(),'Upcoming events'=>Event::where('starts_at','>=',now())->count()]]);} }
