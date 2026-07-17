<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class InstagramFeed
{
    public function posts(int $limit = 6): array
    {
        $token = config('services.instagram.access_token');
        $userId = config('services.instagram.user_id');

        if (! $token || ! $userId) {
            return [];
        }

        return Cache::remember('instagram-api-feed-v2', now()->addMinutes(30), function () use ($token, $userId, $limit) {
            try {
                $response = Http::timeout(8)->retry(1, 200)->get(
                    'https://graph.instagram.com/'.$userId.'/media',
                    [
                        'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,children{media_type,media_url,thumbnail_url}',
                        'limit' => $limit,
                        'access_token' => $token,
                    ]
                )->throw()->json('data', []);

                return collect($response)->map(function (array $item) {
                    $media = $item;
                    if (($item['media_type'] ?? null) === 'CAROUSEL_ALBUM' && ! empty($item['children']['data'][0])) {
                        $media = $item['children']['data'][0];
                    }

                    return [
                        'title' => trim($item['caption'] ?? 'View this post on Instagram'),
                        'url' => $item['permalink'] ?? config('services.instagram.profile_url'),
                        'image' => ($media['media_type'] ?? null) === 'VIDEO'
                            ? ($media['thumbnail_url'] ?? null)
                            : ($media['media_url'] ?? null),
                        'date' => $item['timestamp'] ?? null,
                        'alternate' => collect(config('services.instagram.alternate_posts', []))
                            ->contains(fn (string $postId) => str_contains($item['permalink'] ?? '', $postId)),
                    ];
                })->filter(fn (array $post) => $post['image'])->values()->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }
}
