<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Video;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class YouTubeSyncService
{
    public function sync(): int
    {
        $key = config('services.youtube.key') ?: Setting::value('youtube_api_key');
        $channel = config('services.youtube.channel_id') ?: Setting::value('youtube_channel_id');

        if (! $channel) {
            throw new \RuntimeException('YouTube channel ID is required.');
        }

        if (! $key) {
            return $this->syncPublicFeed($channel);
        }

        try {
            $channelData = Http::retry(2, 500)->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'contentDetails', 'id' => $channel, 'key' => $key,
            ])->throw()->json();
            $uploads = data_get($channelData, 'items.0.contentDetails.relatedPlaylists.uploads');
            if (! $uploads) {
                throw new \RuntimeException('Channel or uploads playlist was not found.');
            }

            $page = null;
            $count = 0;
            do {
                $payload = Http::get('https://www.googleapis.com/youtube/v3/playlistItems', [
                    'part' => 'snippet,contentDetails', 'playlistId' => $uploads, 'maxResults' => 50,
                    'pageToken' => $page, 'key' => $key,
                ])->throw()->json();
                $ids = collect($payload['items'] ?? [])->pluck('contentDetails.videoId')->filter();
                $details = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                    'part' => 'contentDetails,statistics,snippet', 'id' => $ids->join(','), 'key' => $key,
                ])->throw()->json();
                foreach ($details['items'] ?? [] as $video) {
                    $snippet = $video['snippet'];
                    $this->saveVideo($video['id'], $snippet['title'], $snippet['description'] ?? null,
                        data_get($snippet, 'thumbnails.high.url') ?? data_get($snippet, 'thumbnails.default.url'),
                        $snippet['publishedAt'] ?? null, data_get($video, 'statistics.viewCount', 0),
                        data_get($video, 'contentDetails.duration'));
                    $count++;
                }
                $page = $payload['nextPageToken'] ?? null;
            } while ($page && $count < 200);

            return $this->recordSuccess($count, 'api');
        } catch (\Throwable $exception) {
            Log::error('YouTube API sync failed; attempting the public feed.', ['message' => $exception->getMessage()]);
            Cache::put('youtube_sync_error', $exception->getMessage(), now()->addHours(12));

            return $this->syncPublicFeed($channel);
        }
    }

    private function syncPublicFeed(string $channel): int
    {
        try {
            $response = Http::retry(2, 500)->get('https://www.youtube.com/feeds/videos.xml', ['channel_id' => $channel])->throw();
            return $this->syncFeedXml($response->body());
        } catch (\Throwable $exception) {
            Log::error('YouTube public feed sync failed', ['message' => $exception->getMessage()]);
            Cache::put('youtube_sync_error', $exception->getMessage(), now()->addHours(12));
            throw $exception;
        }
    }

    public function syncFeedXml(string $contents): int
    {
        $xml = simplexml_load_string($contents);
        if (! $xml) {
            throw new \RuntimeException('The public YouTube feed returned invalid XML.');
        }

        $count = 0;
        foreach ($xml->entry as $entry) {
                $youtube = $entry->children('http://www.youtube.com/xml/schemas/2015');
                $mediaNamespace = 'http://search.yahoo.com/mrss/';
                $group = $entry->children($mediaNamespace)->group;
                $media = $group->children($mediaNamespace);
                $community = $media->community->children($mediaNamespace);
                $videoId = (string) $youtube->videoId;
                if (! $videoId) {
                    continue;
                }
                $this->saveVideo($videoId, (string) $entry->title, (string) $media->description,
                    (string) $media->thumbnail->attributes()->url, (string) $entry->published,
                    (int) $community->statistics->attributes()->views, null);
            $count++;
        }

        return $this->recordSuccess($count, 'rss');
    }

    private function saveVideo(string $id, string $title, ?string $description, ?string $thumbnail,
        ?string $publishedAt, int $views, ?string $duration): void
    {
        Video::updateOrCreate(['youtube_id' => $id], [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$id,
            'description' => $description,
            'thumbnail_url' => $thumbnail,
            'playlist' => 'Latest uploads',
            'published_at' => $publishedAt,
            'duration' => $duration,
            'view_count' => $views,
        ]);
    }

    private function recordSuccess(int $count, string $source): int
    {
        Cache::forget('youtube_sync_error');
        Cache::put('youtube_last_sync', ['at' => now()->toIso8601String(), 'count' => $count, 'source' => $source], now()->addDays(7));

        return $count;
    }
}
