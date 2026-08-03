<?php

namespace Tests\Feature;

use App\Models\StintPlan;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StintPlanSharingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(PreventRequestForgery::class);
        config()->set('services.stint_planner.share_key', 'team-secret');
    }

    public function test_a_plan_can_be_published_and_read_by_an_overlay(): void
    {
        $response = $this->postJson(route('stint-planner.publish'), [
            'share_key' => 'team-secret',
            'plan' => $this->planPayload(),
        ])->assertOk()->assertJsonStructure(['token', 'updated_at', 'overlays' => ['compact', 'schedule']]);

        $token = $response->json('token');
        $this->assertDatabaseHas(StintPlan::class, ['token' => $token]);
        $this->get(route('stint-overlay', ['token' => $token, 'mode' => 'compact']))->assertOk()->assertSee('stint-overlay.js');
        $this->get(route('stint-overlay.data', $token))->assertOk()->assertJsonPath('plan.event', '24h Spa');
    }

    public function test_the_share_key_is_required(): void
    {
        $this->postJson(route('stint-planner.publish'), ['share_key' => 'wrong', 'plan' => $this->planPayload()])->assertForbidden();
    }

    public function test_a_published_plan_can_be_sent_to_discord(): void
    {
        Http::fake(['discord.com/*' => Http::response(['id' => 'message-1'])]);
        config()->set('services.discord.bot_token', 'test-token');
        config()->set('services.discord.stint_channel', 'stint-channel');
        $token = $this->postJson(route('stint-planner.publish'), ['share_key' => 'team-secret', 'plan' => $this->planPayload()])->json('token');

        $this->postJson(route('stint-planner.discord'), ['share_key' => 'team-secret', 'plan_token' => $token])
            ->assertOk()->assertJsonPath('message', 'Race plan sent to Discord.');

        Http::assertSent(fn ($request) => str_contains($request->url(), '/channels/stint-channel/messages')
            && $request['embeds'][0]['title'] === '🏁 24h Spa'
            && $request['allowed_mentions']['parse'] === []);
    }

    private function planPayload(): array
    {
        return [
            'event' => '24h Spa', 'sim' => 'iRacing', 'car' => 'Ferrari 296 GT3', 'class' => 'GT3',
            'track' => 'Spa-Francorchamps', 'race_date' => '2026-08-15', 'start_time' => '13:45',
            'race_mins' => 1440, 'lap_secs' => 138, 'fuel_per_lap' => 4, 'fuel_unit' => 'L', 'capacity' => 101,
            'drivers' => ['Hayden Sweet', 'Stijn Donckerwolke'],
            'availability' => [[
                'driver' => 'Hayden Sweet', 'from' => '2026-08-15T12:00:00.000Z', 'to' => '2026-08-15T18:00:00.000Z',
                'from_label' => 'Sat 15 Aug, 13:00', 'to_label' => 'Sat 15 Aug, 19:00',
            ]],
            'schedule' => [[
                'stint' => 1, 'driver' => 'Hayden Sweet', 'standby' => 'Stijn Donckerwolke',
                'start' => '2026-08-15T12:45:00.000Z', 'end' => '2026-08-15T13:37:54.000Z',
                'start_label' => 'Sat 15 Aug, 13:45', 'end_label' => 'Sat 15 Aug, 14:37',
                'drive_mins' => 52.9, 'laps' => 23, 'fuel' => 92, 'start_target' => 100,
                'availability_status' => 'Available', 'notes' => 'Opening stint',
            ]],
        ];
    }
}
