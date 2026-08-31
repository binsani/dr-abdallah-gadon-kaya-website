@extends('layouts.public')
@section('title',$title.' — Dr. Gadon Kaya')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">Explore the archive</p><h1>{{$title}}</h1></div></header>
<section class="section"><div class="container"><div class="row g-4">
@forelse($items as $item)<div class="col-md-6 col-lg-4"><article class="card h-100">
@php $listImage=$item->cover_image ?? $item->featured_image ?? null; @endphp
@if($listImage)<img class="card-img-top" loading="lazy" decoding="async" width="640" height="360" src="{{Storage::url($listImage)}}" alt="{{$item->title}}">@endif
<div class="card-body p-4"><p class="meta">{{ $type==='event' ? $item->starts_at?->format('M j, Y · g:i A') : ucfirst($type) }}</p><h4>{{$item->title}}</h4><p>{{Str::limit(strip_tags($item->description ?? $item->body ?? ''),140)}}</p>
@if($type==='article')<a href="{{route('article',$item)}}">Read article →</a>
@elseif($type==='audio')<audio controls preload="none" class="w-100"><source src="{{Storage::url($item->file_path)}}"></audio>
@elseif($type==='book' && $item->external_url)<a class="btn btn-outline-success" href="{{$item->external_url}}">View publication</a>
@elseif($type==='book' && $item->file_path)<a class="btn btn-outline-success" href="{{Storage::url($item->file_path)}}" download>Download PDF</a>
@endif</div></article></div>
@empty<div class="col-12"><div class="empty">No items have been published here yet.</div></div>@endforelse
</div><div class="mt-4">{{$items->links()}}</div></div></section>
@endsection
