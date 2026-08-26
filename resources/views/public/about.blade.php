@extends('layouts.public')
@section('title','Biography — Dr. Gadon Kaya')
@section('content')
<header class="page-hero"><div class="container"><p class="eyebrow">About</p><h1>Biography & scholarly journey</h1></div></header>
<section class="section"><div class="container"><div class="row g-5"><div class="col-lg-7"><h2>{{\App\Models\Setting::value('about_heading','A teacher rooted in scholarship')}}</h2><div class="article-body">{!! \App\Models\Setting::value('about_body','<p>Dr. Abdallah Usman Gadon Kaya is a Nigerian Sunni Islamic scholar, Imam, lecturer and da‘wah teacher known for accessible lessons grounded in the Qur’an and Sunnah.</p><p>His educational journey includes Kano Legal School, the Islamic University of Madinah, and advanced postgraduate study at Bayero University Kano.</p>') !!}</div></div><div class="col-lg-5"><div class="timeline">@forelse($timeline as $entry)<div class="timeline-item"><strong class="eyebrow">{{$entry->year}}</strong><h4>{{$entry->title}}</h4><p>{{$entry->description}}</p></div>@empty<p class="text-muted">Biography milestones will appear here.</p>@endforelse</div></div></div></div></section>
@endsection
