<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings', ['settings' => Setting::where('is_encrypted', false)->pluck('value', 'key')]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'required|max:120', 'site_tagline' => 'nullable|max:180',
            'hero_eyebrow' => 'nullable|max:180', 'hero_title' => 'nullable|max:180', 'hero_highlight' => 'nullable|max:180', 'hero_description' => 'nullable|max:500',
            'about_heading' => 'nullable|max:180', 'about_excerpt' => 'nullable|max:1000', 'about_body' => 'nullable|string|max:20000',
            'daily_quote' => 'nullable|max:1000', 'daily_quote_source' => 'nullable|max:180', 'footer_description' => 'nullable|max:500',
            'contact_heading' => 'nullable|max:180', 'contact_description' => 'nullable|max:1000', 'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|max:50', 'contact_address' => 'nullable|max:500',
            'facebook_url' => 'nullable|url|max:500', 'tiktok_url' => 'nullable|url|max:500', 'youtube_url' => 'nullable|url|max:500',
            'instagram_url' => 'nullable|url|max:500', 'whatsapp_url' => 'nullable|url|max:500', 'telegram_url' => 'nullable|url|max:500',
            'youtube_channel_id' => 'nullable|max:100', 'youtube_api_key' => 'nullable|max:255', 'google_analytics_id' => 'nullable|max:50',
            'maintenance_mode' => 'nullable|boolean', 'logo' => 'nullable|image|max:5120', 'favicon' => 'nullable|image|max:2048', 'hero_image' => 'nullable|image|max:8192',
        ]);

        foreach (['logo', 'favicon', 'hero_image'] as $field) {
            if ($request->hasFile($field)) {
                $old = Setting::value($field);
                if ($old) Storage::disk('public')->delete($old);
                $data[$field] = $request->file($field)->store('branding', 'public');
            }
        }
        $data['maintenance_mode'] = $request->boolean('maintenance_mode') ? '1' : '0';
        if (isset($data['about_body'])) $data['about_body'] = preg_replace('#<(script|iframe|object|embed)[^>]*>.*?</\\1>#is', '', $data['about_body']);

        foreach ($data as $key => $value) {
            $encrypted = $key === 'youtube_api_key';
            if ($encrypted && blank($value)) continue;
            Setting::updateOrCreate(['key' => $key], ['value' => $encrypted ? Crypt::encryptString($value) : $value, 'is_encrypted' => $encrypted]);
        }

        ActivityLog::create(['user_id'=>auth()->id(),'action'=>'updated site settings','subject_type'=>Setting::class,'properties'=>['keys'=>array_keys($data)]]);
        return back()->with('success', 'Website content and settings saved.');
    }
}
