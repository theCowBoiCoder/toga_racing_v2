<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DiscordAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_discord_login_uses_the_identify_scope_and_a_state_token(): void
    {
        config()->set('services.discord.client_id', 'client-123');
        config()->set('services.discord.client_secret', 'secret-456');

        $response = $this->get('/auth/discord');

        $response->assertRedirectContains('https://discord.com/oauth2/authorize?');
        $location = $response->headers->get('Location');
        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

        $this->assertSame('client-123', $query['client_id']);
        $this->assertSame('identify', $query['scope']);
        $this->assertSame(route('discord.callback'), $query['redirect_uri']);
        $this->assertNotEmpty($query['state']);
        $this->assertSame($query['state'], session('discord_oauth_state'));
    }

    public function test_discord_callback_stores_the_verified_user_identity(): void
    {
        config()->set('services.discord.client_id', 'client-123');
        config()->set('services.discord.client_secret', 'secret-456');
        Http::fake([
            'discord.com/api/v10/oauth2/token' => Http::response(['access_token' => 'user-token']),
            'discord.com/api/v10/users/@me' => Http::response([
                'id' => '987654321',
                'username' => 'testdriver',
                'global_name' => 'Test Driver',
            ]),
        ]);

        $this->withSession(['discord_oauth_state' => 'valid-state'])
            ->get('/auth/discord/callback?code=auth-code&state=valid-state')
            ->assertRedirect(route('results').'#submit-result')
            ->assertSessionHas('discord_user.id', '987654321')
            ->assertSessionHas('discord_user.username', 'testdriver')
            ->assertSessionHas('discord_user.display_name', 'Test Driver');

        Http::assertSentCount(2);
    }

    public function test_discord_login_can_return_an_admin_to_the_gallery(): void
    {
        config()->set('services.discord.client_id', 'client-123');
        config()->set('services.discord.client_secret', 'secret-456');
        Http::fake([
            'discord.com/api/v10/oauth2/token' => Http::response(['access_token' => 'user-token']),
            'discord.com/api/v10/users/@me' => Http::response([
                'id' => '987654321',
                'username' => 'testdriver',
                'global_name' => 'Test Driver',
            ]),
        ]);

        $login = $this->get('/auth/discord?return=gallery');
        $login->assertRedirectContains('https://discord.com/oauth2/authorize?');

        $state = session('discord_oauth_state');

        $this->get('/auth/discord/callback?code=auth-code&state='.$state)
            ->assertRedirect(route('gallery'))
            ->assertSessionHas('discord_user.id', '987654321');
    }

    public function test_discord_callback_rejects_an_invalid_state(): void
    {
        Http::fake();

        $this->withSession(['discord_oauth_state' => 'expected-state'])
            ->get('/auth/discord/callback?code=auth-code&state=wrong-state')
            ->assertRedirect(route('results').'#submit-result')
            ->assertSessionHasErrors('discord_auth');

        Http::assertNothingSent();
    }
}
