<?php

namespace App\Http\Controllers;

use App\Jobs\AcceptDriverApplication;
use App\Models\DriverApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DiscordInteractionController extends Controller
{
    private const ADMINISTRATOR = 0x8;

    private const MANAGE_GUILD = 0x20;

    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->hasValidSignature($request)) {
            return response()->json(['message' => 'Invalid request signature.'], 401);
        }

        $payload = $request->json()->all();
        if (($payload['type'] ?? null) === 1) {
            return response()->json(['type' => 1]);
        }

        if (($payload['type'] ?? null) !== 3) {
            return $this->ephemeral('Unsupported Discord interaction.');
        }

        $permissions = (int) data_get($payload, 'member.permissions', 0);
        if (($permissions & self::ADMINISTRATOR) === 0 && ($permissions & self::MANAGE_GUILD) === 0) {
            return $this->ephemeral('Only a TOGA Racing admin can accept driver applications.');
        }

        $customId = (string) data_get($payload, 'data.custom_id');
        if (! preg_match('/^driver_accept:(\d+)$/', $customId, $matches)) {
            return $this->ephemeral('Unknown application action.');
        }

        $adminId = (string) data_get($payload, 'member.user.id');
        $adminName = (string) (data_get($payload, 'member.nick')
            ?: data_get($payload, 'member.user.global_name')
            ?: data_get($payload, 'member.user.username')
            ?: 'Discord admin');
        $discordApplicationId = (string) ($payload['application_id'] ?? '');
        $interactionToken = (string) ($payload['token'] ?? '');

        $application = DriverApplication::find($matches[1]);
        if (! $application) {
            return $this->ephemeral('That driver application could not be found.');
        }

        if ($application->welcome_email_sent_at) {
            return $this->updateButton($customId, 'Accepted and emailed', true);
        }

        if ($discordApplicationId === '' || $interactionToken === '') {
            return $this->ephemeral('Discord did not provide enough information to process this application.');
        }

        AcceptDriverApplication::dispatch(
            (int) $matches[1],
            $adminId,
            $adminName,
            $customId,
            $discordApplicationId,
            $interactionToken,
        )->onConnection('database')->onQueue('emails');

        return response()->json(['type' => 6]);
    }

    private function hasValidSignature(Request $request): bool
    {
        $signature = (string) $request->header('X-Signature-Ed25519');
        $timestamp = (string) $request->header('X-Signature-Timestamp');
        $publicKey = (string) config('services.discord.public_key');

        if (! function_exists('sodium_crypto_sign_verify_detached')
            || strlen($signature) !== 128
            || strlen($publicKey) !== 64
            || ! ctype_xdigit($signature)
            || ! ctype_xdigit($publicKey)
            || $timestamp === '') {
            return false;
        }

        try {
            return sodium_crypto_sign_verify_detached(
                hex2bin($signature),
                $timestamp.$request->getContent(),
                hex2bin($publicKey),
            );
        } catch (Throwable) {
            return false;
        }
    }

    private function ephemeral(string $message): JsonResponse
    {
        return response()->json([
            'type' => 4,
            'data' => [
                'content' => $message,
                'flags' => 64,
            ],
        ]);
    }

    private function updateButton(string $customId, string $label, bool $disabled): JsonResponse
    {
        return response()->json([
            'type' => 7,
            'data' => $this->buttonComponents($customId, $label, $disabled, 3),
        ]);
    }

    private function buttonComponents(string $customId, string $label, bool $disabled, int $style): array
    {
        return [
            'components' => [[
                'type' => 1,
                'components' => [[
                    'type' => 2,
                    'style' => $style,
                    'label' => $label,
                    'custom_id' => $customId,
                    'disabled' => $disabled,
                ]],
            ]],
        ];
    }
}
