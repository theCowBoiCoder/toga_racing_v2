<?php

namespace Tests\Feature;

use App\Mail\DriverAccepted;
use App\Jobs\AcceptDriverApplication;
use App\Models\DriverApplication;
use App\Models\SponsorEnquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_submit_an_application(): void
    {
        Mail::fake();
        Http::fake(['discord.com/*' => Http::response(['id' => 'message-1'])]);
        Queue::fake();
        config()->set('services.discord.bot_token', 'test-token');

        $response = $this->post('/join', [
            'website' => '',
            'started_at' => now()->subSeconds(5)->timestamp,
            'name' => 'Test Driver',
            'email' => 'driver@example.com',
            'country' => 'United Kingdom',
            'timezone' => 'Europe/London',
            'age' => 25,
            'discord' => 'testdriver',
            'simulators' => ['iRacing', 'Le Mans Ultimate'],
            'car_class' => 'GT3',
            'experience' => 'Several seasons of league and endurance racing.',
            'availability' => 'Weekday evenings and most weekends.',
            'motivation' => 'I want to improve with a reliable and competitive team.',
            'profile_links' => 'https://example.com/profile',
        ]);

        $response->assertRedirect(route('enquiry.thanks', 'driver'));
        $this->assertDatabaseHas(DriverApplication::class, ['email' => 'driver@example.com']);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/channels/1527599269392023633/messages')
            && $request['embeds'][0]['title'] === 'New driver application'
            && $request['components'][0]['components'][0]['custom_id'] === 'driver_accept:1');
    }

    public function test_discord_admin_can_accept_a_driver_and_send_the_welcome_email_once(): void
    {
        Mail::fake();
        Http::fake(['discord.com/*' => Http::response(['id' => 'message-1'])]);

        $application = DriverApplication::create([
            'name' => 'Test Driver',
            'email' => 'driver@example.com',
            'country' => 'United Kingdom',
            'timezone' => 'Europe/London',
            'age' => 25,
            'discord' => 'testdriver',
            'simulators' => ['iRacing'],
            'car_class' => 'GT3',
            'experience' => 'Several seasons of league and endurance racing.',
            'availability' => 'Weekday evenings and most weekends.',
            'motivation' => 'I want to improve with a reliable and competitive team.',
        ]);

        $payload = [
            'type' => 3,
            'application_id' => '98765',
            'token' => 'interaction-token',
            'data' => ['custom_id' => 'driver_accept:'.$application->id],
            'member' => [
                'permissions' => '8',
                'user' => ['id' => '12345', 'username' => 'Hayden'],
            ],
        ];

        $firstResponse = $this->signedDiscordRequest($payload);

        $firstResponse->assertOk()->assertJsonPath('type', 6);

        $queuedJob = null;
        Queue::assertPushed(AcceptDriverApplication::class, function (AcceptDriverApplication $job) use (&$queuedJob) {
            $queuedJob = $job;

            return true;
        });
        $queuedJob->handle();

        $secondResponse = $this->signedDiscordRequest($payload);
        $secondResponse->assertOk()->assertJsonPath('type', 7);

        Mail::assertSent(DriverAccepted::class, 1);
        Mail::assertSent(DriverAccepted::class, fn (DriverAccepted $mail) => $mail->hasTo('driver@example.com'));
        $this->assertDatabaseHas('driver_applications', [
            'id' => $application->id,
            'status' => 'accepted',
            'accepted_by_discord_id' => '12345',
            'accepted_by_discord_name' => 'Hayden',
        ]);
        $this->assertNotNull($application->fresh()->welcome_email_sent_at);
        Http::assertSent(fn ($request) => $request->method() === 'PATCH'
            && str_contains($request->url(), '/webhooks/98765/interaction-token/messages/@original')
            && $request['components'][0]['components'][0]['disabled'] === true);
    }

    public function test_discord_interactions_require_a_valid_signature(): void
    {
        $this->withHeaders([
            'X-Signature-Ed25519' => str_repeat('0', 128),
            'X-Signature-Timestamp' => (string) now()->timestamp,
        ])->postJson('/discord/interactions', ['type' => 1])->assertUnauthorized();
    }

    public function test_sponsor_can_submit_an_enquiry(): void
    {
        Mail::fake();
        Http::fake(['discord.com/*' => Http::response(['id' => 'message-2'])]);
        config()->set('services.discord.bot_token', 'test-token');

        $response = $this->post('/partners', [
            'website' => '',
            'started_at' => now()->subSeconds(5)->timestamp,
            'company' => 'Example Racing Ltd',
            'contact_name' => 'Alex Sponsor',
            'email' => 'alex@example.com',
            'company_website' => 'https://example.com',
            'partnership_type' => 'Sponsorship',
            'budget' => 'Product and financial support',
            'goals' => 'Reach an engaged and growing sim racing audience.',
            'message' => 'We would like to discuss a season-long team partnership.',
        ]);

        $response->assertRedirect(route('enquiry.thanks', 'sponsor'));
        $this->assertDatabaseHas(SponsorEnquiry::class, ['company' => 'Example Racing Ltd']);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/channels/1527599240405323887/messages')
            && $request['embeds'][0]['title'] === 'New partnership enquiry');
    }

    public function test_driver_application_requires_an_age(): void
    {
        $response = $this->post('/join', [
            'website' => '',
            'started_at' => now()->subSeconds(5)->timestamp,
            'name' => 'Test Driver',
            'email' => 'driver@example.com',
            'country' => 'United Kingdom',
            'timezone' => 'Europe/London',
            'discord' => 'testdriver',
            'simulators' => ['iRacing'],
            'car_class' => 'GT3',
            'experience' => 'Several seasons of league and endurance racing.',
            'availability' => 'Weekday evenings and most weekends.',
            'motivation' => 'I want to improve with a reliable and competitive team.',
        ]);

        $response->assertSessionHasErrors('age');
        $this->assertDatabaseCount('driver_applications', 0);
    }

    public function test_honeypot_rejects_spam(): void
    {
        $this->post('/join', ['website' => 'spam'])->assertSessionHasErrors('website');
        $this->assertDatabaseCount('driver_applications', 0);
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
