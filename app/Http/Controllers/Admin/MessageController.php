<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\ContactMessage;
class MessageController extends Controller { public function index(){return view('admin.messages',['items'=>ContactMessage::latest()->paginate(25)]);} public function show(ContactMessage $message){$message->update(['is_read'=>true]);return view('admin.message',compact('message'));} public function destroy(ContactMessage $message){$message->delete();return back()->with('success','Message deleted.');} }
