<?php
namespace App\Http\Controllers;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
class ContactController extends Controller { public function create(){return view('public.contact');} public function store(Request $r){$data=$r->validate(['name'=>'required|max:100','email'=>'required|email|max:150','phone'=>'nullable|max:30','subject'=>'required|max:180','message'=>'required|max:5000']);ContactMessage::create($data);return back()->with('success',__('site.message_sent'));} }
