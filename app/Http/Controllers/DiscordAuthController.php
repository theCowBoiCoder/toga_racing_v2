<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class DiscordAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $returnRoute = $this->returnRouteName((string) $request->query('return', 'results'));

        if (! config('services.discord.client_id') || ! config('services.discord.client_secret')) {
            return redirect($this->returnUrl($returnRoute))
                ->withErrors(['discord_auth' => 'Discord sign-in has not been configured yet.']);
        }

        $state = Str::random(64);
        $request->session()->put('discord_oauth_state', $state);
        $request->session()->put('discord_return_route', $returnRoute);

        return redirect()->away('https://discord.com/oauth2/authorize?'.http_build_query([
            'client_id' => config('services.discord.client_id'),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => 'identify',
            'state' => $state,
        ]));
    }

    public function callback(Request $request): RedirectResponse
    {
        $returnRoute = $this->returnRouteName((string) $request->session()->pull('discord_return_route', 'results'));
        $expectedState = (string) $request->session()->pull('discord_oauth_state', '');
        $receivedState = (string) $request->query('state', '');

        if ($request->query('error') || $expectedState === '' || ! hash_equals($expectedState, $receivedState)) {
            return $this->failed('Discord sign-in was cancelled or could not be verified. Please try again.', $returnRoute);
        }

        $code = (string) $request->query('code', '');
        if ($code === '') {
            return $this->failed('Discord did not return an authorization code. Please try again.', $returnRoute);
        }

        try {
            $token = Http::asForm()->timeout(8)->post('https://discord.com/api/v10/oauth2/token', [
                'client_id' => config('services.discord.client_id'),
                'client_secret' => config('services.discord.client_secret'),
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri(),
            ])->throw()->json();

            $discordUser = Http::withToken($token['access_token'] ?? '')
                ->timeout(8)
                ->get('https://discord.com/api/v10/users/@me')
                ->throw()
                ->json();

            if (empty($discordUser['id']) || empty($discordUser['username'])) {
                throw new \RuntimeException('Discord returned an incomplete user profile.');
            }

            $request->session()->regenerate();
            $request->session()->put('discord_user', [
                'id' => (string) $discordUser['id'],
                'username' => (string) $discordUser['username'],
                'display_name' => (string) (($discordUser['global_name'] ?? null) ?: $discordUser['username']),
            ]);

            return redirect($this->returnUrl($returnRoute))
                ->with('discord_auth_success', 'Signed in with Discord.');
        } catch (Throwable $exception) {
            Log::warning('Discord OAuth sign-in failed.', ['error' => $exception->getMessage()]);

            return $this->failed('Discord sign-in failed. Please try again.', $returnRoute);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('discord_user');
        $request->session()->regenerateToken();

        return redirect($this->returnUrl($this->returnRouteName((string) $request->query('return', 'results'))));
    }

    private function redirectUri(): string
    {
        return config('services.discord.redirect_uri') ?: route('discord.callback');
    }

    private function failed(string $message, string $returnRoute): RedirectResponse
    {
        return redirect($this->returnUrl($returnRoute))->withErrors(['discord_auth' => $message]);
    }

    private function returnRouteName(string $route): string
    {
        return in_array($route, ['gallery', 'results'], true) ? $route : 'results';
    }

    private function returnUrl(string $route): string
    {
        return $route === 'gallery' ? route('gallery') : route('results').'#submit-result';
    }
}
