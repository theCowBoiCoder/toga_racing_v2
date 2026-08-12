<?php

namespace App\Jobs;

use App\Mail\DriverAccepted;
use App\Models\DriverApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AcceptDriverApplication implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(
        public int $applicationId,
        public string $adminId,
        public string $adminName,
        public string $customId,
        public string $discordApplicationId,
        public string $interactionToken,
    ) {}

    public function backoff(): array
    {
        return [2, 5];
    }

    public function handle(): void
    {
        DB::transaction(function () {
            $application = DriverApplication::query()->lockForUpdate()->findOrFail($this->applicationId);

            if ($application->welcome_email_sent_at) {
                return;
            }

            Mail::to($application->email)->send(new DriverAccepted($application));

            $application->update([
                'status' => 'accepted',
                'accepted_at' => now(),
                'welcome_email_sent_at' => now(),
                'accepted_by_discord_id' => $this->adminId,
                'accepted_by_discord_name' => $this->adminName,
            ]);
        });

        $this->editDiscordButton('Accepted and emailed', true, 3);
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Driver acceptance email failed permanently.', [
            'application_id' => $this->applicationId,
            'error' => $exception->getMessage(),
        ]);

        $sent = DriverApplication::find($this->applicationId)?->welcome_email_sent_at !== null;
        $this->editDiscordButton(
            $sent ? 'Accepted and emailed' : 'Email failed — retry',
            $sent,
            $sent ? 3 : 4,
        );
    }

    private function editDiscordButton(string $label, bool $disabled, int $style): void
    {
        try {
            Http::timeout(5)->patch(
                'https://discord.com/api/v10/webhooks/'.$this->discordApplicationId.'/'.$this->interactionToken.'/messages/@original',
                [
                    'components' => [[
                        'type' => 1,
                        'components' => [[
                            'type' => 2,
                            'style' => $style,
                            'label' => $label,
                            'custom_id' => $this->customId,
                            'disabled' => $disabled,
                        ]],
                    ]],
                ],
            )->throw();
        } catch (Throwable $exception) {
            Log::warning('Discord acceptance button could not be updated.', [
                'application_id' => $this->applicationId,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
