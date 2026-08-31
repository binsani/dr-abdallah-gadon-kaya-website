@extends('layouts.public')
@section('title','Gallery — Dr. Gadon Kaya')
@section('description','Official photographs and channel highlights of Dr. Abdallah Usman Gadon Kaya.')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Official media archive</p><h1>Gallery</h1><p class="lead">Photographs and highlights collected from official channels.</p></div></header>
<section class="section"><div class="container">
@forelse($albums as $album)<div class="mb-5"><h2 class="h3">{{$album->title}}</h2>@if($album->description)<p class="text-muted">{{$album->description}}</p>@endif<div class="row g-3">
@foreach($album->images as $image) @if($image->display_url)<div class="col-6 col-md-4 col-xl-3"><figure class="card h-100 overflow-hidden"><img src="{{$image->display_url}}" alt="{{$image->alt_text ?: $album->title}}" loading="lazy" decoding="async" width="480" height="360" class="card-img-top" style="aspect-ratio:4/3;object-fit:cover"><figcaption class="card-body p-3"><small>{{$image->alt_text}}</small>@if($image->source_url)<br><a href="{{$image->source_url}}" target="_blank" rel="noopener" class="small">View original source</a>@endif</figcaption></figure></div>@endif @endforeach
</div></div>@empty<div class="empty">Gallery images will appear here soon.</div>@endforelse
</div></section>
@endsection
