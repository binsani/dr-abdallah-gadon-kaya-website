<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->index(['is_hidden', 'published_at'], 'videos_visibility_published_index');
            $table->index(['playlist', 'published_at'], 'videos_playlist_published_index');
        });
        Schema::table('articles', fn (Blueprint $table) => $table->index(['status', 'published_at'], 'articles_status_published_index'));
        Schema::table('events', fn (Blueprint $table) => $table->index('starts_at', 'events_starts_at_index'));
        Schema::table('gallery_images', fn (Blueprint $table) => $table->index(['gallery_album_id', 'sort_order'], 'gallery_images_album_sort_index'));
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex('videos_visibility_published_index');
            $table->dropIndex('videos_playlist_published_index');
        });
        Schema::table('articles', fn (Blueprint $table) => $table->dropIndex('articles_status_published_index'));
        Schema::table('events', fn (Blueprint $table) => $table->dropIndex('events_starts_at_index'));
        Schema::table('gallery_images', fn (Blueprint $table) => $table->dropIndex('gallery_images_album_sort_index'));
    }
};
