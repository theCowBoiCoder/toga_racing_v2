<?php

namespace App\Services;

use App\Models\DriverApplication;
use App\Models\SponsorEnquiry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscordNotifier
{
    public function driver(DriverApplication $application): void
    {
        $this->send(config('services.discord.driver_channel'), [
            'title' => 'New driver application',
            'description' => $this->clean($application->motivation, 900),
            'color' => 1706828,
            'fields' => [
                ['name' => 'Driver', 'value' => $this->clean($application->name), 'inline' => true],
                ['name' => 'Email', 'value' => $this->clean($application->email), 'inline' => true],
                ['name' => 'Discord', 'value' => $this->clean($application->discord), 'inline' => true],
                ['name' => 'Location', 'value' => $this->clean($application->country.' · '.$application->timezone), 'inline' => true],
                ['name' => 'Simulators', 'value' => $this->clean(implode(', ', $application->simulators)), 'inline' => true],
                ['name' => 'Preferred class', 'value' => $this->clean($application->car_class), 'inline' => true],
                ['name' => 'Experience', 'value' => $this->clean($application->experience, 700)],
                ['name' => 'Availability', 'value' => $this->clean($application->availability, 500)],
                ['name' => 'Profile links', 'value' => $this->clean($application->profile_links ?: 'Not supplied', 500)],
            ],
            'footer' => ['text' => 'Toga Racing · Application #'.$application->id],
            'timestamp' => $application->created_at->toIso8601String(),
        ]);
    }

    public function sponsor(SponsorEnquiry $enquiry): void
    {
        $this->send(config('services.discord.partner_channel'), [
            'title' => 'New partnership enquiry',
            'description' => $this->clean($enquiry->message, 900),
            'color' => 15592941,
            'fields' => [
                ['name' => 'Company', 'value' => $this->clean($enquiry->company), 'inline' => true],
                ['name' => 'Contact', 'value' => $this->clean($enquiry->contact_name), 'inline' => true],
                ['name' => 'Email', 'value' => $this->clean($enquiry->email), 'inline' => true],
                ['name' => 'Type', 'value' => $this->clean($enquiry->partnership_type), 'inline' => true],
                ['name' => 'Budget/support', 'value' => $this->clean($enquiry->budget ?: 'Not supplied'), 'inline' => true],
                ['name' => 'Website', 'value' => $this->clean($enquiry->website ?: 'Not supplied'), 'inline' => true],
                ['name' => 'Partnership goals', 'value' => $this->clean($enquiry->goals, 700)],
            ],
            'footer' => ['text' => 'Toga Racing · Partnership #'.$enquiry->id],
            'timestamp' => $enquiry->created_at->toIso8601String(),
        ]);
    }

    private function send(?string $channelId, array $embed): void
    {
        $token = config('services.discord.bot_token');
        if (! $token || ! $channelId) {
            return;
        }

        try {
            Http::withToken($token, 'Bot')->timeout(8)->post(
                'https://discord.com/api/v10/channels/'.$channelId.'/messages',
                ['embeds' => [$embed], 'allowed_mentions' => ['parse' => []]]
            )->throw();
        } catch (\Throwable $exception) {
            Log::warning('Discord form notification failed.', ['channel_id' => $channelId, 'error' => $exception->getMessage()]);
        }
    }

    private function clean(?string $value, int $limit = 250): string
    {
        $value = preg_replace('/@(everyone|here)/i', '@​$1', trim((string) $value));

        return mb_substr($value ?: 'Not supplied', 0, $limit);
    }
}
