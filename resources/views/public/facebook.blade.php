@extends('layouts.public')
@section('title','Facebook Updates — Dr. Gadon Kaya')
@section('description','Read recent updates from the official Facebook page of Dr. Abdallah Usman Gadon Kaya.')
@section('content')
@php
$facebook=\App\Models\Setting::value('facebook_url','https://www.facebook.com/DrabdallahGadonkaya');
$plugin='https://www.facebook.com/plugins/page.php?'.http_build_query(['href'=>$facebook,'tabs'=>'timeline','width'=>500,'height'=>720,'small_header'=>'false','adapt_container_width'=>'true','hide_cover'=>'false','show_facepile'=>'true']);
@endphp
<header class="page-hero"><div class="container"><p class="eyebrow">Official social channel</p><h1>Facebook updates</h1><p class="lead">Follow announcements, lessons and community updates from the official page.</p></div></header>
<section class="section section-cream"><div class="container text-center"><div class="mx-auto bg-white rounded shadow-sm p-2" style="max-width:540px;min-height:720px"><iframe loading="lazy" src="{{$plugin}}" width="500" height="720" style="border:none;overflow:hidden;width:100%;max-width:500px" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" title="Official Facebook page"></iframe></div><p class="mt-4"><a class="btn btn-primary" href="{{$facebook}}" target="_blank" rel="noopener">Open the official Facebook page</a></p></div></section>
@endsection
