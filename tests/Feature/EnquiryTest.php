<?php

namespace Tests\Feature;

use App\Models\DriverApplication;
use App\Models\SponsorEnquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_submit_an_application(): void
    {
        Mail::fake();
        Http::fake(['discord.com/*' => Http::response(['id' => 'message-1'])]);
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
            && $request['embeds'][0]['title'] === 'New driver application');
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

    public function test_honeypot_rejects_spam(): void
    {
        $this->post('/join', ['website' => 'spam'])->assertSessionHasErrors('website');
        $this->assertDatabaseCount('driver_applications', 0);
    }
}
