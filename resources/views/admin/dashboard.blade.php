@extends('layouts.admin')
@section('content')
@if($errors->has('youtube'))<div class="alert alert-warning">{{$errors->first('youtube')}}</div>@endif
<div class="d-flex justify-content-end mb-3"><form method="post" action="{{route('admin.youtube.sync')}}">@csrf<button class="btn btn-success">Sync YouTube now</button></form></div>
<div class="row g-4">@foreach($stats as $label=>$value)<div class="col-sm-6 col-xl-3"><div class="card border-0 shadow-sm p-4"><small class="text-muted">{{$label}}</small><strong class="display-6">{{$value}}</strong></div></div>@endforeach</div>
<div class="card border-0 shadow-sm mt-4 p-4"><h2 class="h5">Welcome</h2><p class="mb-0">Use the menu to publish lectures, articles, events and other resources. Save the API credentials and channel ID in Site Settings, then use “Sync YouTube now”.</p></div>
<div class="d-flex flex-wrap gap-2 mt-3"><a class="btn btn-outline-success" href="{{route('admin.media')}}">Media Library</a>@if(auth()->user()->role==='super_admin')<a class="btn btn-outline-success" href="{{route('admin.users')}}">Users & Roles</a><a class="btn btn-outline-success" href="{{route('admin.activity')}}">Activity Log</a>@endif</div>
@endsection
