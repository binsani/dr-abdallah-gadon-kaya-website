<?php

namespace App\Http\Controllers;

use App\Models\{Article, AudioTrack, Book, Event, GalleryAlbum, TimelineEntry, Video};
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        return view('public.home', [
            'videos' => Video::query()->select(['id', 'youtube_id', 'title', 'slug', 'thumbnail_url', 'playlist', 'published_at', 'is_featured'])->where('is_hidden', false)->orderByDesc('is_featured')->latest('published_at')->limit(6)->get(),
            'articles' => Article::query()->select(['id', 'title', 'slug', 'body', 'published_at'])->where('status', 'published')->latest('published_at')->limit(3)->get(),
            'events' => Event::query()->select(['id', 'title', 'slug', 'starts_at', 'venue'])->where('starts_at', '>=', now())->orderBy('starts_at')->limit(3)->get(),
        ]);
    }

    public function about()
    {
        return view('public.about', ['timeline' => TimelineEntry::query()->select(['id', 'year', 'title', 'description', 'sort_order'])->orderBy('sort_order')->get()]);
    }

    public function videos(Request $request)
    {
        $items = Video::query()
            ->select(['id', 'youtube_id', 'title', 'slug', 'thumbnail_url', 'playlist', 'published_at', 'view_count'])
            ->where('is_hidden', false)
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q')->trim().'%'))
            ->when($request->filled('playlist'), fn ($query) => $query->where('playlist', $request->string('playlist')))
            ->latest('published_at')->paginate(12)->withQueryString();

        return view('public.videos', [
            'items' => $items,
            'playlists' => Video::query()->whereNotNull('playlist')->distinct()->orderBy('playlist')->pluck('playlist'),
        ]);
    }

    public function articles()
    {
        return $this->listing(__('site.articles'), Article::query()->select(['id', 'title', 'slug', 'body', 'cover_image', 'published_at'])->where('status', 'published')->latest('published_at')->paginate(9), 'article');
    }

    public function article(Article $article)
    {
        abort_unless($article->status === 'published', 404);
        return view('public.detail', compact('article'));
    }

    public function audio()
    {
        return $this->listing(__('site.audio'), AudioTrack::query()->select(['id', 'title', 'slug', 'description', 'cover_image', 'file_path', 'created_at'])->latest()->paginate(12), 'audio');
    }

    public function events()
    {
        return $this->listing(__('site.events'), Event::query()->select(['id', 'title', 'slug', 'description', 'featured_image', 'starts_at', 'venue'])->orderByDesc('starts_at')->paginate(12), 'event');
    }

    public function gallery()
    {
        return view('public.gallery', [
            'albums' => GalleryAlbum::query()
                ->select(['id', 'title', 'slug', 'description', 'created_at'])
                ->with(['images:id,gallery_album_id,image_path,external_url,source_url,alt_text,sort_order'])
                ->latest()->get(),
        ]);
    }

    public function books()
    {
        return $this->listing(__('site.books'), Book::query()->select(['id', 'title', 'slug', 'description', 'cover_image', 'file_path', 'external_url', 'created_at'])->latest()->paginate(12), 'book');
    }

    public function tiktok() { return view('public.tiktok'); }
    public function facebook() { return view('public.facebook'); }

    public function search(Request $request)
    {
        $term = $request->validate(['q' => 'required|string|max:100'])['q'];
        return view('public.search', [
            'term' => $term,
            'articles' => Article::query()->select(['id', 'title', 'slug'])->where('status', 'published')->where('title', 'like', "%{$term}%")->limit(20)->get(),
            'videos' => Video::query()->select(['id', 'title', 'slug'])->where('is_hidden', false)->where('title', 'like', "%{$term}%")->limit(20)->get(),
            'audio' => AudioTrack::query()->select(['id', 'title', 'slug'])->where('title', 'like', "%{$term}%")->limit(20)->get(),
        ]);
    }

    public function locale(string $locale)
    {
        abort_unless(in_array($locale, ['en', 'ha'], true), 404);
        session(['locale' => $locale]);
        return back();
    }

    private function listing(string $title, $items, string $type)
    {
        return view('public.list', compact('title', 'items', 'type'));
    }
}
