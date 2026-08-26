<?php
namespace App\Http\Middleware;
use Closure;
class EnsureRole { public function handle($request, Closure $next, ...$roles){ abort_unless($request->user() && in_array($request->user()->role,$roles),403); return $next($request); } }
