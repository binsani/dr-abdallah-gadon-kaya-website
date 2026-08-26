<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class AuthController extends Controller { public function create(){return view('auth.login');} public function store(Request $r){$c=$r->validate(['email'=>'required|email','password'=>'required|string']); if(!Auth::attempt($c,$r->boolean('remember'))){return back()->withErrors(['email'=>'The credentials do not match our records.'])->onlyInput('email');}$r->session()->regenerate();return redirect()->intended(route('admin.dashboard'));} public function destroy(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect('/');} }
