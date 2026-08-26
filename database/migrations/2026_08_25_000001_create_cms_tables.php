<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('slug')->unique(); $t->string('type')->default('article'); $t->timestamps(); });
        Schema::create('videos', function (Blueprint $t) { $t->id(); $t->string('youtube_id')->nullable()->unique(); $t->string('title'); $t->string('slug')->unique(); $t->longText('description')->nullable(); $t->string('thumbnail_url')->nullable(); $t->string('playlist')->nullable(); $t->timestamp('published_at')->nullable(); $t->boolean('is_featured')->default(false); $t->boolean('is_hidden')->default(false); $t->unsignedBigInteger('view_count')->default(0); $t->string('duration')->nullable(); $t->timestamps(); });
        Schema::create('articles', function (Blueprint $t) { $t->id(); $t->foreignId('category_id')->nullable()->constrained()->nullOnDelete(); $t->string('title'); $t->string('slug')->unique(); $t->longText('body'); $t->string('status')->default('draft'); $t->timestamp('published_at')->nullable(); $t->string('meta_title')->nullable(); $t->text('meta_description')->nullable(); $t->string('cover_image')->nullable(); $t->json('translations')->nullable(); $t->timestamps(); });
        Schema::create('audio_tracks', function (Blueprint $t) { $t->id(); $t->string('title'); $t->string('slug')->unique(); $t->text('description')->nullable(); $t->string('file_path'); $t->string('category')->nullable(); $t->string('cover_image')->nullable(); $t->string('duration')->nullable(); $t->timestamps(); });
        Schema::create('events', function (Blueprint $t) { $t->id(); $t->string('title'); $t->string('slug')->unique(); $t->longText('description')->nullable(); $t->string('venue')->nullable(); $t->timestamp('starts_at'); $t->timestamp('ends_at')->nullable(); $t->boolean('is_recurring')->default(false); $t->string('featured_image')->nullable(); $t->unsignedInteger('interest_count')->default(0); $t->timestamps(); });
        Schema::create('gallery_albums', function (Blueprint $t) { $t->id(); $t->string('title'); $t->string('slug')->unique(); $t->text('description')->nullable(); $t->string('cover_image')->nullable(); $t->timestamps(); });
        Schema::create('gallery_images', function (Blueprint $t) { $t->id(); $t->foreignId('gallery_album_id')->constrained()->cascadeOnDelete(); $t->string('image_path'); $t->string('alt_text')->nullable(); $t->unsignedInteger('sort_order')->default(0); $t->timestamps(); });
        Schema::create('books', function (Blueprint $t) { $t->id(); $t->string('title'); $t->string('slug')->unique(); $t->text('description')->nullable(); $t->string('file_path')->nullable(); $t->string('external_url')->nullable(); $t->string('cover_image')->nullable(); $t->timestamps(); });
        Schema::create('contact_messages', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('email'); $t->string('phone')->nullable(); $t->string('subject'); $t->text('message'); $t->boolean('is_read')->default(false); $t->timestamps(); });
        Schema::create('settings', function (Blueprint $t) { $t->id(); $t->string('key')->unique(); $t->longText('value')->nullable(); $t->boolean('is_encrypted')->default(false); $t->timestamps(); });
        Schema::create('timeline_entries', function (Blueprint $t) { $t->id(); $t->string('year'); $t->string('title'); $t->text('description')->nullable(); $t->unsignedInteger('sort_order')->default(0); $t->timestamps(); });
        Schema::create('media', function (Blueprint $t) { $t->id(); $t->string('name'); $t->string('file_path'); $t->string('mime_type'); $t->unsignedBigInteger('size')->default(0); $t->string('alt_text')->nullable(); $t->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete(); $t->timestamps(); });
        Schema::create('activity_logs', function (Blueprint $t) { $t->id(); $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $t->string('action'); $t->string('subject_type')->nullable(); $t->unsignedBigInteger('subject_id')->nullable(); $t->json('properties')->nullable(); $t->timestamps(); });
    }
    public function down(): void { foreach (['activity_logs','media','timeline_entries','settings','contact_messages','books','gallery_images','gallery_albums','events','audio_tracks','articles','videos','categories'] as $table) Schema::dropIfExists($table); }
};
