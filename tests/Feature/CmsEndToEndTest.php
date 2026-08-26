<?php

namespace Tests\Feature;

use App\Models\{ActivityLog,Article,AudioTrack,Book,ContactMessage,Event,GalleryAlbum,GalleryImage,Media,Setting,User,Video};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsEndToEndTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'super_admin', 'password' => Hash::make('password1234')]);
    }

    public function test_every_public_page_language_search_and_contact_flow(): void
    {
        foreach (['/', '/about', '/videos', '/tiktok', '/facebook', '/audio', '/articles', '/events', '/gallery', '/books', '/contact', '/search?q=official'] as $uri) {
            $this->get($uri)->assertOk()->assertDontSee('Fatal error', false);
        }

        $this->get('/language/ha')->assertRedirect();
        $this->get('/')->assertOk()->assertSee('Tarihin Rayuwa');
        $this->get('/')->assertSee(asset('images/dr-abdallah-homepage.jpg'));

        $this->post('/contact', ['name'=>'Test Visitor','email'=>'visitor@example.com','phone'=>'08000000000','subject'=>'Test enquiry','message'=>'End-to-end contact test.'])->assertRedirect();
        $message = ContactMessage::where('subject', 'Test enquiry')->firstOrFail();
        $this->actingAs($this->admin())->get(route('admin.messages.show', $message))->assertOk();
        $this->assertTrue($message->fresh()->is_read);
        $this->delete(route('admin.messages.destroy', $message))->assertRedirect();
        $this->assertDatabaseMissing('contact_messages', ['id'=>$message->id]);
    }

    public function test_admin_login_and_all_content_crud_with_uploads(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $this->post('/admin/login', ['email'=>$admin->email,'password'=>'password1234'])->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Dashboard');

        $cases = [
            'videos' => ['title'=>'CRUD Video','youtube_id'=>'crudvideo01','playlist'=>'Tafsir','thumbnail_url'=>'https://example.com/video.jpg','is_featured'=>1],
            'articles' => ['title'=>'CRUD Article','body'=>'<p>Original article body</p>','status'=>'published','published_at'=>now()->format('Y-m-d H:i:s'),'cover_image'=>UploadedFile::fake()->image('article.jpg')],
            'audio' => ['title'=>'CRUD Audio','description'=>'Audio description','category'=>'Hadith','file_path'=>UploadedFile::fake()->create('lecture.mp3', 100, 'audio/mpeg')],
            'events' => ['title'=>'CRUD Event','description'=>'Event description','venue'=>'Kano','starts_at'=>now()->addDay()->format('Y-m-d H:i:s')],
            'albums' => ['title'=>'CRUD Gallery Album','description'=>'Album description','cover_image'=>UploadedFile::fake()->image('album.jpg')],
            'books' => ['title'=>'CRUD Book','description'=>'Book description','file_path'=>UploadedFile::fake()->create('book.pdf', 100, 'application/pdf'),'cover_image'=>UploadedFile::fake()->image('book.jpg')],
            'timeline' => ['title'=>'CRUD Timeline','year'=>'2026','description'=>'Timeline description','sort_order'=>10],
        ];

        $models = ['videos'=>Video::class,'articles'=>Article::class,'audio'=>AudioTrack::class,'events'=>Event::class,'albums'=>GalleryAlbum::class,'books'=>Book::class];
        foreach ($cases as $type => $payload) {
            $this->post(route('admin.content.store', $type), $payload)->assertRedirect(route('admin.content.index', $type));
            if (! isset($models[$type])) continue;
            $model = $models[$type]::where('title', $payload['title'])->firstOrFail();
            $update = collect($payload)->except(['cover_image','file_path'])->all();
            $update['title'] = 'Updated '.$payload['title'];
            $this->put(route('admin.content.update', [$type, $model->id]), $update)->assertRedirect(route('admin.content.index', $type));
            $this->assertSame('Updated '.$payload['title'], $model->fresh()->title);
            $this->delete(route('admin.content.destroy', [$type, $model->id]))->assertRedirect();
            $this->assertDatabaseMissing($model->getTable(), ['id'=>$model->id]);
        }

        $this->assertGreaterThan(0, ActivityLog::count());
    }

    public function test_media_users_settings_and_video_visibility_controls(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $this->actingAs($admin);

        $this->post(route('admin.media.upload'), ['files'=>[UploadedFile::fake()->image('media.jpg')],'alt_text'=>'Test image'])->assertRedirect();
        $media = Media::where('name','media.jpg')->firstOrFail();
        Storage::disk('public')->assertExists($media->file_path);
        $this->delete(route('admin.media.delete',$media))->assertRedirect();
        Storage::disk('public')->assertMissing($media->file_path);

        $this->post(route('admin.users.save'), ['name'=>'CRUD Editor','email'=>'editor-crud@example.com','role'=>'editor','password'=>'editorpass123'])->assertRedirect();
        $editor = User::where('email','editor-crud@example.com')->firstOrFail();
        $this->post(route('admin.users.save',$editor), ['name'=>'Updated Editor','email'=>'editor-crud@example.com','role'=>'editor','password'=>''])->assertRedirect();
        $this->assertSame('Updated Editor',$editor->fresh()->name);
        $this->delete(route('admin.users.delete',$editor))->assertRedirect();
        $this->assertDatabaseMissing('users',['id'=>$editor->id]);

        $this->put(route('admin.settings.update'), ['site_name'=>'Test Site','hero_title'=>'Editable Homepage Title','facebook_url'=>'https://www.facebook.com/DrabdallahGadonkaya','tiktok_url'=>'https://www.tiktok.com/@dr_abdallah_gadon_kaya','contact_email'=>'admin@example.com','youtube_channel_id'=>'test-channel','youtube_api_key'=>'secret-key','maintenance_mode'=>1])->assertRedirect();
        $this->put(route('admin.settings.update'), ['site_name'=>'Test Site','contact_email'=>'admin@example.com','youtube_channel_id'=>'test-channel','youtube_api_key'=>'','maintenance_mode'=>1])->assertRedirect();
        $this->assertDatabaseHas('settings',['key'=>'site_name','value'=>'Test Site']);
        $this->assertSame('secret-key',Setting::value('youtube_api_key'));
        $this->get('/')->assertSee('Editable Homepage Title')->assertSee('being updated');
        $this->get('/tiktok')->assertSee('data-embed-type="creator"',false)->assertSee('@dr_abdallah_gadon_kaya');
        $this->get('/facebook')->assertSee('facebook.com/plugins/page.php',false)->assertSee('DrabdallahGadonkaya');

        $featured = Video::create(['title'=>'Featured Visibility Test','slug'=>'featured-visibility-test','youtube_id'=>'featuretest','is_featured'=>true,'is_hidden'=>false,'published_at'=>now()->subYear()]);
        $hidden = Video::create(['title'=>'Hidden Visibility Test','slug'=>'hidden-visibility-test','youtube_id'=>'hiddentest','is_hidden'=>true,'published_at'=>now()]);
        $newer = Video::create(['title'=>'Newer Non-featured Video','slug'=>'newer-non-featured-video','youtube_id'=>'newertest','is_featured'=>false,'is_hidden'=>false,'published_at'=>now()]);
        $this->get('/')->assertSeeInOrder([$featured->title,$newer->title])->assertDontSee($hidden->title);
        $this->get('/videos?q=Featured+Visibility+Test')->assertSee($featured->title);
        $this->get('/videos?q=Hidden+Visibility+Test')->assertDontSee('youtube-nocookie.com/embed/hiddentest');
        $this->get('/admin/activity')->assertOk();
    }

    public function test_real_public_disk_uploads_are_served_and_removed_with_content(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);

        $this->post(route('admin.content.store','articles'), ['title'=>'Disk Article','body'=>'<p>Disk test</p>','status'=>'published','published_at'=>now()->format('Y-m-d H:i:s'),'cover_image'=>UploadedFile::fake()->image('disk-article.jpg')])->assertRedirect();
        $article=Article::where('title','Disk Article')->firstOrFail();
        Storage::disk('public')->assertExists($article->cover_image);
        $this->get('/articles')->assertOk()->assertSee(Storage::url($article->cover_image));

        $this->post(route('admin.content.store','audio'), ['title'=>'Disk Audio','file_path'=>UploadedFile::fake()->create('disk-audio.mp3',100,'audio/mpeg')])->assertRedirect();
        $audio=AudioTrack::where('title','Disk Audio')->firstOrFail();
        Storage::disk('public')->assertExists($audio->file_path);
        $this->get('/audio')->assertOk()->assertSee(Storage::url($audio->file_path));

        $this->post(route('admin.content.store','books'), ['title'=>'Disk Book','file_path'=>UploadedFile::fake()->create('disk-book.pdf',100,'application/pdf')])->assertRedirect();
        $book=Book::where('title','Disk Book')->firstOrFail();
        Storage::disk('public')->assertExists($book->file_path);
        $this->get('/books')->assertOk()->assertSee('Download PDF')->assertSee(Storage::url($book->file_path));

        foreach([['articles',$article,'cover_image'],['audio',$audio,'file_path'],['books',$book,'file_path']] as [$type,$model,$field]){$path=$model->{$field};$this->delete(route('admin.content.destroy',[$type,$model->id]))->assertRedirect();Storage::disk('public')->assertMissing($path);}
    }

    public function test_youtube_public_feed_imports_complete_video_metadata(): void
    {
        Http::fake(['youtube.com/*'=>Http::response('<?xml version="1.0"?><feed xmlns="http://www.w3.org/2005/Atom" xmlns:yt="http://www.youtube.com/xml/schemas/2015" xmlns:media="http://search.yahoo.com/mrss/"><entry><yt:videoId>rss-test-id</yt:videoId><title>RSS Test Lecture</title><published>2026-08-25T20:00:35+00:00</published><media:group><media:description>Lecture description</media:description><media:thumbnail url="https://i.ytimg.com/vi/rss-test-id/hqdefault.jpg"/><media:community><media:statistics views="4321"/></media:community></media:group></entry></feed>',200,['Content-Type'=>'application/xml'])]);
        config(['services.youtube.key'=>null,'services.youtube.channel_id'=>'UC-test-channel']);
        $this->artisan('youtube:sync')->assertSuccessful();
        $this->assertDatabaseHas('videos',['youtube_id'=>'rss-test-id','title'=>'RSS Test Lecture','thumbnail_url'=>'https://i.ytimg.com/vi/rss-test-id/hqdefault.jpg','view_count'=>4321]);
    }

    public function test_gallery_images_can_be_uploaded_edited_deleted_and_synced(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());
        $album = GalleryAlbum::create(['title'=>'Managed Gallery','slug'=>'managed-gallery']);

        $this->post(route('admin.gallery.images.store',$album),['images'=>[UploadedFile::fake()->image('portrait.jpg')],'alt_text'=>'Official portrait'])->assertRedirect();
        $uploaded = GalleryImage::where('gallery_album_id',$album->id)->where('alt_text','Official portrait')->firstOrFail();
        Storage::disk('public')->assertExists($uploaded->image_path);
        $this->get('/gallery')->assertSee('Official portrait');

        $this->put(route('admin.gallery.images.update',$uploaded),['alt_text'=>'Updated portrait','source_url'=>'https://example.com/original','sort_order'=>8])->assertRedirect();
        $this->assertSame('Updated portrait',$uploaded->fresh()->alt_text);
        $path=$uploaded->image_path;
        $this->delete(route('admin.gallery.images.destroy',$uploaded))->assertRedirect();
        Storage::disk('public')->assertMissing($path);

        Video::updateOrCreate(['youtube_id'=>'gallery-sync-test'],['title'=>'Official Gallery Sync Test','slug'=>'official-gallery-sync-test','thumbnail_url'=>'https://i.ytimg.com/vi/gallery-sync-test/hqdefault.jpg','published_at'=>now()]);
        $this->post(route('admin.gallery.sync'))->assertRedirect();
        $this->assertDatabaseHas('gallery_images',['source_url'=>'https://www.youtube.com/watch?v=gallery-sync-test','alt_text'=>'Official Gallery Sync Test']);
    }

    public function test_official_channel_video_can_be_featured_and_hidden(): void
    {
        $video = Video::where('playlist','Latest uploads')->firstOrFail();
        $this->actingAs($this->admin());
        $payload = ['title'=>$video->title,'slug'=>$video->slug,'youtube_id'=>$video->youtube_id,'playlist'=>$video->playlist,'thumbnail_url'=>$video->thumbnail_url,'published_at'=>$video->published_at?->format('Y-m-d H:i:s')];

        $this->put(route('admin.content.update',['videos',$video->id]),$payload+['is_featured'=>1])->assertRedirect();
        $this->assertTrue($video->fresh()->is_featured);
        $this->get('/')->assertSee($video->title);

        $this->put(route('admin.content.update',['videos',$video->id]),$payload+['is_hidden'=>1])->assertRedirect();
        $this->assertTrue($video->fresh()->is_hidden);
        $this->get('/videos?q='.urlencode($video->title))->assertDontSee('youtube-nocookie.com/embed/'.$video->youtube_id);
    }
}
