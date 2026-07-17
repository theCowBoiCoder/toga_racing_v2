<?php

namespace Tests\Feature;

use App\Services\InstagramFeed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InstagramFeedTest extends TestCase
{
    public function test_it_maps_instagram_media_for_the_homepage(): void
    {
        config()->set('services.instagram.user_id', '123');
        config()->set('services.instagram.access_token', 'test-token');
        Cache::forget('instagram-api-feed-v2');

        Http::fake([
            'graph.instagram.com/*' => Http::response(['data' => [[
                'id' => '1',
                'caption' => 'Race day',
                'media_type' => 'IMAGE',
                'media_url' => 'https://example.com/race.jpg',
                'permalink' => 'https://instagram.com/p/example',
                'timestamp' => '2026-07-16T12:00:00+0000',
            ]]]),
        ]);

        $posts = app(InstagramFeed::class)->posts();

        $this->assertCount(1, $posts);
        $this->assertSame('Race day', $posts[0]['title']);
        $this->assertSame('https://example.com/race.jpg', $posts[0]['image']);
    }

    public function test_it_uses_the_original_first_carousel_image(): void
    {
        config()->set('services.instagram.user_id', '123');
        config()->set('services.instagram.access_token', 'test-token');
        Cache::forget('instagram-api-feed-v2');

        Http::fake([
            'graph.instagram.com/*' => Http::response(['data' => [[
                'id' => '2',
                'caption' => 'Carousel post',
                'media_type' => 'CAROUSEL_ALBUM',
                'media_url' => 'https://example.com/cropped-cover.jpg',
                'permalink' => 'https://instagram.com/p/carousel',
                'timestamp' => '2026-07-17T12:00:00+0000',
                'children' => ['data' => [[
                    'media_type' => 'IMAGE',
                    'media_url' => 'https://example.com/original-slide.jpg',
                ]]],
            ]]]),
        ]);

        $posts = app(InstagramFeed::class)->posts();

        $this->assertSame('https://example.com/original-slide.jpg', $posts[0]['image']);
    }
}
