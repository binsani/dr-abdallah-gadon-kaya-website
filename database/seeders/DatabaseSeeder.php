<?php

namespace Database\Seeders;

use App\Models\{Article,Category,Event,Setting,TimelineEntry,User,Video};
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(['email' => 'admin@example.com'], ['name'=>'Site Administrator','password'=>Hash::make(env('ADMIN_PASSWORD','ChangeMeNow!')),'role'=>'super_admin']);
        $category=Category::firstOrCreate(['slug'=>'guidance'],['name'=>'Guidance','type'=>'article']);
        Article::firstOrCreate(['slug'=>'welcome-to-the-official-archive'],['category_id'=>$category->id,'title'=>'Welcome to the Official Knowledge Archive','body'=>'<p>This website brings together lectures, articles, events and publications in one trusted home.</p><p>Follow the official channels and return regularly for new lessons.</p>','status'=>'published','published_at'=>now()->subDay(),'meta_description'=>'Welcome to the official digital archive of Dr. Abdallah Usman Gadon Kaya.']);
        foreach([["KARATUN LITTAFIN AL KABA'IR DARASI NA 42",'yq1Cg1w6CDI'],['Lecture Daga Jihar Maradi Na Jamhuriyyar Niger || Dr. Abdallah Usman Gadon Kaya','sLzATmT0Wbs']] as $v) Video::firstOrCreate(['youtube_id'=>$v[1]],['title'=>$v[0],'slug'=>str($v[0])->slug().'-'.$v[1],'playlist'=>'Latest uploads','thumbnail_url'=>'https://i.ytimg.com/vi/'.$v[1].'/hqdefault.jpg','published_at'=>now()->subDays(2)]);
        Event::firstOrCreate(['slug'=>'weekly-tafsir-session'],['title'=>'Weekly Tafsir Session','description'=>'A community lesson and reflection on the Qur’an.','venue'=>'Kano, Nigeria','starts_at'=>now()->addWeek()->setTime(17,0)]);
        foreach([['Kano','Foundational legal and Islamic studies'],['Madinah','Islamic University of Madinah'],['Bayero University Kano','Master’s and PhD studies'],['Present','Imam, lecturer and da‘wah teacher']] as $i=>$t) TimelineEntry::firstOrCreate(['title'=>$t[1]],['year'=>$t[0],'sort_order'=>$i+1]);
        foreach([
            'site_name'=>'Dr. Abdallah Usman Gadon Kaya','site_tagline'=>'Official Website','hero_eyebrow'=>'Knowledge · Guidance · Community','hero_title'=>'Dr. Abdallah Usman','hero_highlight'=>'Gadon Kaya','hero_description'=>'Islamic scholar, Imam, lecturer and da‘wah teacher dedicated to making authentic knowledge accessible.','about_heading'=>'A life devoted to learning and service','about_excerpt'=>'From Kano Legal School to the Islamic University of Madinah and Bayero University Kano, Dr. Gadon Kaya’s journey reflects a sustained commitment to scholarship, teaching and community guidance.','daily_quote'=>'Indeed, those who fear Allah among His servants are those who have knowledge.','daily_quote_source'=>'Qur’an 35:28','footer_description'=>'Sharing authentic Islamic knowledge through lectures, scholarship and community service.','contact_heading'=>'Contact the office','contact_description'=>'For lecture invitations, media enquiries and general correspondence, use this form.','facebook_url'=>'https://www.facebook.com/DrabdallahGadonkaya','tiktok_url'=>'https://www.tiktok.com/@dr_abdallah_gadon_kaya','youtube_url'=>'https://www.youtube.com/@DrAbdallahUsmanGadonKaya','telegram_url'=>'https://t.me/drabdallahgadonkaya','youtube_channel_id'=>'UCJo7v-APDGnj2Rz3N9nIMqw','maintenance_mode'=>'0',
        ] as $key=>$value) Setting::firstOrCreate(['key'=>$key],['value'=>$value]);
    }
}
