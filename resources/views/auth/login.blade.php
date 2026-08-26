<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin sign in</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body style="background:#123f2d"><main class="container min-vh-100 d-flex align-items-center justify-content-center">
<form class="card p-4 shadow" style="width:100%;max-width:420px" method="post" action="{{route('login.store')}}">@csrf
<h1 class="h3">Site Manager</h1><p class="text-muted">Sign in to manage official content.</p>
<div class="mb-3"><label for="email">Email</label><input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{old('email')}}" required autofocus>@error('email')<div class="invalid-feedback">{{$message}}</div>@enderror</div>
<div class="mb-3"><label for="password">Password</label><input id="password" class="form-control" type="password" name="password" required></div>
<label class="mb-3"><input type="checkbox" name="remember"> Remember me</label><button class="btn btn-success w-100">Sign in</button><a class="text-center mt-3" href="{{route('home')}}">← Back to website</a>
</form></main></body></html>
