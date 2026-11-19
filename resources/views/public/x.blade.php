@extends('layouts.public')
@section('title','X Updates — Dr. Gadon Kaya')
@section('description','Read updates from the confirmed X account of Dr. Abdallah Usman Gadon Kaya.')
@section('content')
@php $x=\App\Models\Setting::value('x_url','https://x.com/abdallahgkaya'); @endphp
<header class="page-hero"><div class="container"><p class="eyebrow">Official social channel</p><h1>X updates</h1><p class="lead">Posts and public updates from @abdallahgkaya.</p></div></header>
<section class="section section-cream"><div class="container"><div class="mx-auto bg-white rounded shadow-sm p-3" style="max-width:720px;min-height:640px"><a class="twitter-timeline" data-height="720" data-dnt="true" href="{{$x}}">Posts by @abdallahgkaya</a><noscript><a class="btn btn-dark" href="{{$x}}" target="_blank" rel="noopener">Open @abdallahgkaya on X</a></noscript></div></div></section>
@endsection
@push('scripts')<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>@endpush
