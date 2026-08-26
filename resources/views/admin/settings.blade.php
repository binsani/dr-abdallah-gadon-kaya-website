@extends('layouts.admin')
@section('heading','Site Content & Branding')
@section('content')
@php $value=fn($key,$default='')=>old($key,$settings[$key]??$default); @endphp
<form method="post" enctype="multipart/form-data" action="{{route('admin.settings.update')}}">@csrf @method('put')
@if($errors->any())<div class="alert alert-danger"><strong>Please correct these fields:</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>@endif

<div class="card border-0 shadow-sm p-4 mb-4"><h2 class="h5">Identity and branding</h2><div class="row g-3">
<div class="col-md-6"><label class="form-label">Website name</label><input class="form-control" name="site_name" value="{{$value('site_name','Dr. Abdallah Usman Gadon Kaya')}}" required></div>
<div class="col-md-6"><label class="form-label">Short tagline</label><input class="form-control" name="site_tagline" value="{{$value('site_tagline')}}"></div>
@foreach(['logo'=>'Logo','favicon'=>'Browser icon','hero_image'=>'Homepage portrait'] as $key=>$label)<div class="col-md-4"><label class="form-label">{{$label}}</label><input class="form-control" type="file" name="{{$key}}" accept="image/*">@if($value($key))<small class="text-success">Current file saved</small>@endif</div>@endforeach
</div></div>

<div class="card border-0 shadow-sm p-4 mb-4"><h2 class="h5">Homepage content</h2><div class="row g-3">
@foreach(['hero_eyebrow'=>'Small heading','hero_title'=>'Main name/title','hero_highlight'=>'Highlighted words','about_heading'=>'About heading','daily_quote_source'=>'Quote source'] as $key=>$label)<div class="col-md-6"><label class="form-label">{{$label}}</label><input class="form-control" name="{{$key}}" value="{{$value($key)}}"></div>@endforeach
@foreach(['hero_description'=>'Hero introduction','about_excerpt'=>'Short biography','daily_quote'=>'Daily Qur’an/Hadith quotation','footer_description'=>'Footer description'] as $key=>$label)<div class="col-12"><label class="form-label">{{$label}}</label><textarea class="form-control" rows="3" name="{{$key}}">{{$value($key)}}</textarea></div>@endforeach
</div></div>

<div class="card border-0 shadow-sm p-4 mb-4"><h2 class="h5">Biography page</h2><label class="form-label">Full biography</label><textarea id="about-editor" class="form-control" rows="14" name="about_body">{{$value('about_body')}}</textarea><p class="small text-muted mt-2">Education milestones remain editable under Biography Timeline.</p></div>

<div class="card border-0 shadow-sm p-4 mb-4"><h2 class="h5">Contact information</h2><div class="row g-3">
@foreach(['contact_heading'=>'Contact heading','contact_email'=>'Public email','contact_phone'=>'Public phone','contact_address'=>'Address'] as $key=>$label)<div class="col-md-6"><label class="form-label">{{$label}}</label><input class="form-control" name="{{$key}}" value="{{$value($key)}}"></div>@endforeach
<div class="col-12"><label class="form-label">Contact-page introduction</label><textarea class="form-control" name="contact_description" rows="3">{{$value('contact_description')}}</textarea></div>
</div></div>

<div class="card border-0 shadow-sm p-4 mb-4"><h2 class="h5">Official social accounts</h2><div class="row g-3">
@foreach(['facebook_url'=>'Facebook','tiktok_url'=>'TikTok','youtube_url'=>'YouTube','instagram_url'=>'Instagram','whatsapp_url'=>'WhatsApp','telegram_url'=>'Telegram'] as $key=>$label)<div class="col-md-6"><label class="form-label">{{$label}} URL</label><input class="form-control" type="url" name="{{$key}}" value="{{$value($key)}}"></div>@endforeach
</div></div>

<div class="card border-0 shadow-sm p-4 mb-4"><h2 class="h5">Integrations</h2><div class="row g-3"><div class="col-md-6"><label class="form-label">YouTube channel ID</label><input class="form-control" name="youtube_channel_id" value="{{$value('youtube_channel_id')}}"></div><div class="col-md-6"><label class="form-label">YouTube API key</label><input class="form-control" type="password" name="youtube_api_key" placeholder="Leave blank to preserve the saved key"></div><div class="col-md-6"><label class="form-label">Google Analytics ID</label><input class="form-control" name="google_analytics_id" value="{{$value('google_analytics_id')}}"></div><div class="col-md-6 d-flex align-items-end"><label class="form-check"><input class="form-check-input" type="checkbox" name="maintenance_mode" value="1" @checked($value('maintenance_mode')==='1')><span class="form-check-label">Show maintenance notice to public visitors</span></label></div></div></div>

<div class="sticky-bottom bg-white border rounded p-3 shadow-sm"><button class="btn btn-success btn-lg">Save all website changes</button></div>
</form>
@endsection
@push('scripts')<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script><script>tinymce.init({selector:'#about-editor',menubar:false,plugins:'link lists',toolbar:'undo redo | blocks | bold italic | bullist numlist | link'});</script>@endpush
