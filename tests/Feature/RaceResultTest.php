<?php

namespace Tests\Feature;

use App\Models\RaceResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RaceResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_result_is_submitted_privately_and_sent_to_discord_for_approval(): void
    {
        Storage::fake('local');
        Http::fake(['discord.com/*' => Http::response(['id' => 'message-1'])]);
        config()->set('services.discord.bot_token', 'test-token');

        $response = $this->withSession($this->discordSession())->post('/results', $this->validResultData());

        $response->assertRedirect(route('enquiry.thanks', 'race-result'));
        $result = RaceResult::firstOrFail();
        $this->assertSame('pending', $result->status);
        $this->assertSame('987654321', $result->submitter_discord_id);
        $this->assertSame('testdriver', $result->submitter_discord_username);
        Storage::disk('local')->assertExists($result->image_path);
        $this->get('/results')->assertOk()->assertSee('No approved results have been published yet.');
        $this->get(route('results.image', $result))->assertNotFound();

        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), '/channels/1538240961375633559/messages')) {
                return false;
            }

            return str_contains($request->body(), 'Race result awaiting approval')
                && str_contains($request->body(), 'result_approve:1')
                && str_contains($request->body(), 'testdriver');
        });
    }

    public function test_a_discord_admin_can_approve_and_publish_a_result(): void
    {
        Storage::fake('local');
        $image = $this->pngUpload();
        $path = $image->store('race-results', 'local');
        $result = RaceResult::create([
            'event_name' => '24 Hours of Spa',
            'event_date' => '2026-08-10',
            'simulator' => 'iRacing',
            'split_number' => 2,
            'starting_position' => 12,
            'finishing_position' => 4,
            'car_class' => 'GT3',
            'team_members' => "Hayden Sweet\nStijn Donckerwolke",
            'image_path' => $path,
            'image_original_name' => 'spa.png',
        ]);

        $payload = [
            'type' => 3,
            'data' => ['custom_id' => 'result_approve:'.$result->id],
            'member' => [
                'permissions' => '8',
                'user' => ['id' => '12345', 'username' => 'Hayden'],
            ],
        ];

        $this->signedDiscordRequest($payload)
            ->assertOk()
            ->assertJsonPath('type', 7)
            ->assertJsonPath('data.components.0.components.0.disabled', true);

        $this->assertDatabaseHas('race_results', [
            'id' => $result->id,
            'status' => 'approved',
            'approved_by_discord_id' => '12345',
            'approved_by_discord_name' => 'Hayden',
        ]);
        $this->assertNotNull($result->fresh()->approved_at);
        $this->get('/results')->assertOk()->assertSee('24 Hours of Spa')->assertSee('10 Aug 2026');
        $this->get(route('results.image', $result))->assertOk();
    }

    public function test_result_submission_validates_required_race_details_and_image(): void
    {
        $this->withSession($this->discordSession())->post('/results', [
            'website' => '',
            'started_at' => now()->subSeconds(5)->timestamp,
        ])->assertSessionHasErrors([
            'event_name',
            'event_date',
            'simulator',
            'split_number',
            'starting_position',
            'finishing_position',
            'car_class',
            'team_members',
            'car_image',
        ]);

        $this->assertDatabaseCount('race_results', 0);
    }

    public function test_result_submission_requires_discord_sign_in(): void
    {
        $this->post('/results', $this->validResultData())
            ->assertRedirect(route('discord.login'));

        $this->assertDatabaseCount('race_results', 0);
        $this->get('/results')
            ->assertOk()
            ->assertSee('SIGN IN TO SUBMIT')
            ->assertDontSee('Send for approval');
    }

    public function test_only_the_configured_discord_admin_can_delete_a_result_and_its_image(): void
    {
        Storage::fake('local');
        $path = $this->pngUpload()->store('race-results', 'local');
        $result = $this->createApprovedResult($path);

        $this->withSession($this->discordSession())
            ->delete(route('results.destroy', $result))
            ->assertForbidden();

        $this->assertDatabaseHas('race_results', ['id' => $result->id]);
        Storage::disk('local')->assertExists($path);

        $this->withSession($this->adminDiscordSession())
            ->get('/results')
            ->assertOk()
            ->assertSee('Delete result');

        $this->withSession($this->adminDiscordSession())
            ->delete(route('results.destroy', $result))
            ->assertRedirect(route('results'))
            ->assertSessionHas('result_deleted');

        $this->assertDatabaseMissing('race_results', ['id' => $result->id]);
        Storage::disk('local')->assertMissing($path);
    }

    private function validResultData(): array
    {
        return [
            'website' => '',
            'started_at' => now()->subSeconds(5)->timestamp,
            'event_name' => '24 Hours of Spa',
            'event_date' => '2026-08-10',
            'simulator' => 'iRacing',
            'split_number' => 2,
            'starting_position' => 12,
            'finishing_position' => 4,
            'car_class' => 'GT3',
            'team_members' => "Hayden Sweet\nStijn Donckerwolke",
            'car_image' => $this->pngUpload(),
        ];
    }

    private function pngUpload(): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');

        return UploadedFile::fake()->createWithContent('spa.png', $png);
    }

    private function discordSession(): array
    {
        return [
            'discord_user' => [
                'id' => '987654321',
                'username' => 'testdriver',
                'display_name' => 'Test Driver',
            ],
        ];
    }

    private function adminDiscordSession(): array
    {
        return [
            'discord_user' => [
                'id' => '321560062231314443',
                'username' => 'togaadmin',
                'display_name' => 'Toga Admin',
            ],
        ];
    }

    private function createApprovedResult(string $path): RaceResult
    {
        return RaceResult::create([
            'event_name' => '24 Hours of Spa',
            'event_date' => '2026-08-10',
            'simulator' => 'iRacing',
            'split_number' => 2,
            'starting_position' => 12,
            'finishing_position' => 4,
            'car_class' => 'GT3',
            'team_members' => 'Hayden Sweet',
            'image_path' => $path,
            'image_original_name' => 'spa.png',
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    private function signedDiscordRequest(array $payload)
    {
        $keyPair = sodium_crypto_sign_keypair();
        $secretKey = sodium_crypto_sign_secretkey($keyPair);
        $publicKey = sodium_crypto_sign_publickey($keyPair);
        $timestamp = (string) now()->timestamp;
        $body = json_encode($payload, JSON_THROW_ON_ERROR);

        config()->set('services.discord.public_key', bin2hex($publicKey));

        return $this->call(
            'POST',
            '/discord/interactions',
            [],
            [],
            [],
            [
                'HTTP_X_SIGNATURE_ED25519' => bin2hex(sodium_crypto_sign_detached($timestamp.$body, $secretKey)),
                'HTTP_X_SIGNATURE_TIMESTAMP' => $timestamp,
                'CONTENT_TYPE' => 'application/json',
            ],
            $body,
        );
    }
}
