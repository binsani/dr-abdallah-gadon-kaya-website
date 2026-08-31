@extends('layouts.public') @section('title','Video Library — Dr. Gadon Kaya') @section('content')<header class="page-hero"><div class="container"><p class="eyebrow">Watch and learn</p><h1>Video library</h1><form class="row g-2 mt-4"><div class="col-md-6"><input class="form-control" name="q" value="{{request('q')}}" placeholder="Search lectures"></div><div class="col-md-4"><select class="form-select" name="playlist"><option value="">All categories</option>@foreach($playlists as $p)<option @selected(request('playlist')==$p)>{{$p}}</option>@endforeach</select></div><div class="col-md-2"><button class="btn btn-gold w-100">Filter</button></div></form></div></header><section class="section"><div class="container"><div class="row g-4">@forelse($items as $v)<div class="col-md-6 col-lg-4"><div class="card h-100"><button class="video-preview" type="button" data-video-id="{{$v->youtube_id}}" aria-label="Play {{$v->title}}"><img loading="lazy" decoding="async" width="480" height="270" src="{{$v->thumbnail_url ?: 'https://i.ytimg.com/vi/'.$v->youtube_id.'/hqdefault.jpg'}}" alt=""><span class="video-play" aria-hidden="true">▶</span></button><div class="card-body"><p class="meta">{{$v->playlist}} · {{number_format($v->view_count)}} views</p><h5>{{$v->title}}</h5></div></div></div>@empty<div class="empty">No videos found.</div>@endforelse</div><div class="mt-4">{{$items->links()}}</div></div></section>@endsection

@push('head')<link rel="stylesheet" href="{{asset('css/videos.css')}}">@endpush
@push('scripts')<script>
document.addEventListener('click', function (event) {
    const preview = event.target.closest('.video-preview');
    if (!preview) return;

    const frame = document.createElement('iframe');
    frame.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(preview.dataset.videoId) + '?autoplay=1';
    frame.title = preview.getAttribute('aria-label').replace(/^Play /, '');
    frame.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
    frame.allowFullscreen = true;
    frame.referrerPolicy = 'strict-origin-when-cross-origin';
    preview.replaceWith(frame);
});
</script>@endpush
