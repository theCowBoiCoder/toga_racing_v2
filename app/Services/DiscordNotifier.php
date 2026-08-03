<?php

namespace App\Services;

use App\Models\DriverApplication;
use App\Models\SponsorEnquiry;
use App\Models\StintPlan;
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

    public function stintPlan(StintPlan $stintPlan): bool
    {
        $plan = $stintPlan->plan;
        $schedule = collect($plan['schedule'] ?? []);
        $fields = [
            ['name' => 'Simulator / car', 'value' => $this->clean(($plan['sim'] ?? '').' · '.($plan['car'] ?? '')), 'inline' => true],
            ['name' => 'Track', 'value' => $this->clean($plan['track'] ?? ''), 'inline' => true],
            ['name' => 'Start', 'value' => $this->clean(($plan['race_date'] ?? '').' '.($plan['start_time'] ?? '').' local'), 'inline' => true],
        ];

        if ($stintPlan->availability_token) {
            $fields[] = ['name' => 'Driver availability', 'value' => '[Submit or update your times]('.route('stint-availability', $stintPlan->availability_token).')'];
        }

        $availability = collect($plan['availability'] ?? [])->groupBy('driver')->map(function ($windows, $driver) {
            $times = $windows->map(fn (array $window) => ($window['from_label'] ?? '?').' - '.($window['to_label'] ?? '?'))->implode("\n");

            return '**'.$this->clean($driver, 70).'** · '.$times;
        })->implode("\n");
        if ($availability !== '') {
            $fields[] = ['name' => 'Availability received', 'value' => mb_substr($availability, 0, 1024)];
        }

        foreach ($schedule->chunk(8) as $chunkIndex => $chunk) {
            $first = $chunk->first()['stint'] ?? 1;
            $last = $chunk->last()['stint'] ?? $first;
            $lines = $chunk->map(function (array $stint) use ($plan) {
                $start = $stint['start_label'] ?? '--:--';
                $driver = $stint['driver'] ?: 'TBC';
                $fuel = rtrim(rtrim(number_format((float) ($stint['start_target'] ?? 0), 1), '0'), '.');

                return sprintf('`%02d` **%s** · %s · %s laps · %s %s', $stint['stint'], $this->clean($driver, 70), $start, $stint['laps'] ?? 0, $fuel, $plan['fuel_unit'] ?? '');
            })->implode("\n");
            $fields[] = ['name' => "Stints {$first}–{$last}", 'value' => mb_substr($lines, 0, 1024)];
        }

        return $this->send(config('services.discord.stint_channel'), [
            'title' => '🏁 '.($plan['event'] ?? 'Toga Racing stint plan'),
            'description' => sprintf('**%s stints · %s minute race**\nPublished from the Toga Racing stint planner.', $schedule->count(), $plan['race_mins'] ?? '—'),
            'color' => 1706828,
            'fields' => $fields,
            'footer' => ['text' => 'Toga Racing · Plan '.$stintPlan->token],
            'timestamp' => $stintPlan->updated_at->toIso8601String(),
        ]);
    }

    private function send(?string $channelId, array $embed): bool
    {
        $token = config('services.discord.bot_token');
        if (! $token || ! $channelId) {
            return false;
        }

        try {
            Http::withToken($token, 'Bot')->timeout(8)->post(
                'https://discord.com/api/v10/channels/'.$channelId.'/messages',
                ['embeds' => [$embed], 'allowed_mentions' => ['parse' => []]]
            )->throw();
            return true;
        } catch (\Throwable $exception) {
            Log::warning('Discord form notification failed.', ['channel_id' => $channelId, 'error' => $exception->getMessage()]);
            return false;
        }
    }

    private function clean(?string $value, int $limit = 250): string
    {
        $value = preg_replace('/@(everyone|here)/i', '@​$1', trim((string) $value));

        return mb_substr($value ?: 'Not supplied', 0, $limit);
    }
}
