<?php
namespace App\Http\Controllers;
use App\Models\{Article,AudioTrack,Book,Event,GalleryAlbum,Setting,TimelineEntry,Video};
use Illuminate\Http\Request;
class PublicController extends Controller {
 public function home(){return view('public.home',['videos'=>Video::where('is_hidden',false)->orderByDesc('is_featured')->latest('published_at')->take(6)->get(),'articles'=>Article::where('status','published')->latest('published_at')->take(3)->get(),'events'=>Event::where('starts_at','>=',now())->orderBy('starts_at')->take(3)->get()]);}
 public function about(){return view('public.about',['timeline'=>TimelineEntry::orderBy('sort_order')->get()]);}
 public function videos(Request $r){$q=Video::where('is_hidden',false); if($r->filled('q'))$q->where('title','like','%'.$r->q.'%'); if($r->filled('playlist'))$q->where('playlist',$r->playlist); return view('public.videos',['items'=>$q->latest('published_at')->paginate(12)->withQueryString(),'playlists'=>Video::whereNotNull('playlist')->distinct()->pluck('playlist')]);}
 public function articles(){return view('public.list',['title'=>__('site.articles'),'items'=>Article::where('status','published')->latest('published_at')->paginate(9),'type'=>'article']);}
 public function article(Article $article){abort_unless($article->status==='published',404);return view('public.detail',compact('article'));}
 public function audio(){return view('public.list',['title'=>__('site.audio'),'items'=>AudioTrack::latest()->paginate(12),'type'=>'audio']);}
 public function events(){return view('public.list',['title'=>__('site.events'),'items'=>Event::orderByDesc('starts_at')->paginate(12),'type'=>'event']);}
 public function gallery(){return view('public.gallery',['albums'=>GalleryAlbum::with('images')->latest()->get()]);}
 public function books(){return view('public.list',['title'=>__('site.books'),'items'=>Book::latest()->paginate(12),'type'=>'book']);}
 public function tiktok(){return view('public.tiktok');}
 public function facebook(){return view('public.facebook');}
 public function search(Request $r){$term=$r->validate(['q'=>'required|string|max:100'])['q']; return view('public.search',['term'=>$term,'articles'=>Article::where('status','published')->where('title','like',"%$term%")->take(20)->get(),'videos'=>Video::where('is_hidden',false)->where('title','like',"%$term%")->take(20)->get(),'audio'=>AudioTrack::where('title','like',"%$term%")->take(20)->get()]);}
 public function locale($locale){abort_unless(in_array($locale,['en','ha']),404);session(['locale'=>$locale]);return back();}
}
