@extends('layouts.public')
@section('title','TikTok Videos — Dr. Gadon Kaya')
@section('description','Watch recent TikTok videos from the official Dr. Abdallah Usman Gadon Kaya account.')
@section('content')
@php $tiktok=\App\Models\Setting::value('tiktok_url','https://www.tiktok.com/@dr_abdallah_gadon_kaya'); $tiktokHandle=ltrim(trim(parse_url($tiktok,PHP_URL_PATH),'/'),'@'); @endphp
<header class="page-hero"><div class="container"><p class="eyebrow">Official social channel</p><h1>TikTok videos</h1><p class="lead">Recent short lessons and reminders from the official account.</p></div></header>
<section class="section section-cream"><div class="container"><div class="mx-auto" style="max-width:780px"><blockquote class="tiktok-embed" cite="{{$tiktok}}" data-unique-id="{{$tiktokHandle}}" data-embed-type="creator" style="max-width:780px;min-width:288px"><section><a target="_blank" rel="noopener" href="{{$tiktok}}?refer=creator_embed">{{'@'.$tiktokHandle}}</a></section></blockquote><noscript><a class="btn btn-dark" href="{{$tiktok}}">View the official TikTok profile</a></noscript></div></div></section>
@endsection
@push('scripts')<script defer src="https://www.tiktok.com/embed.js"></script>@endpush
