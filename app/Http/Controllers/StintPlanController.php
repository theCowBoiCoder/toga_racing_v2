<?php

namespace App\Http\Controllers;

use App\Models\StintPlan;
use App\Services\DiscordNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StintPlanController extends Controller
{
    public function __construct(private readonly DiscordNotifier $discord) {}

    public function publish(Request $request): JsonResponse
    {
        $this->authorizeShare($request);
        $validated = $this->validatePlan($request);
        $token = $request->string('plan_token')->toString();
        $stintPlan = $token ? StintPlan::where('token', $token)->first() : null;

        if ($stintPlan) {
            $stintPlan->update(['plan' => $validated['plan']]);
        } else {
            $stintPlan = StintPlan::create(['token' => (string) Str::uuid(), 'plan' => $validated['plan']]);
        }

        return response()->json([
            'token' => $stintPlan->token,
            'updated_at' => $stintPlan->updated_at->toIso8601String(),
            'overlays' => [
                'compact' => route('stint-overlay', ['token' => $stintPlan->token, 'mode' => 'compact']),
                'schedule' => route('stint-overlay', ['token' => $stintPlan->token, 'mode' => 'schedule']),
            ],
        ]);
    }

    public function discord(Request $request): JsonResponse
    {
        $this->authorizeShare($request);
        $data = $request->validate(['plan_token' => ['required', 'uuid']]);
        $stintPlan = StintPlan::where('token', $data['plan_token'])->firstOrFail();

        if (! $this->discord->stintPlan($stintPlan)) {
            return response()->json(['message' => 'Discord is not configured for stint plans.'], 422);
        }

        return response()->json(['message' => 'Race plan sent to Discord.']);
    }

    public function overlay(string $token, Request $request): View
    {
        $stintPlan = StintPlan::where('token', $token)->firstOrFail();
        $mode = in_array($request->query('mode'), ['compact', 'schedule'], true) ? $request->query('mode') : 'compact';

        return view('stint-overlay', ['stintPlan' => $stintPlan, 'mode' => $mode]);
    }

    public function data(string $token): JsonResponse
    {
        $stintPlan = StintPlan::where('token', $token)->firstOrFail();

        return response()->json(['plan' => $stintPlan->plan, 'updated_at' => $stintPlan->updated_at->toIso8601String()]);
    }

    private function authorizeShare(Request $request): void
    {
        $configured = (string) config('services.stint_planner.share_key');
        $provided = (string) $request->input('share_key');

        abort_if($configured === '', 503, 'The stint planner share key is not configured.');
        abort_unless(hash_equals($configured, $provided), 403, 'The team share key is incorrect.');
    }

    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'plan_token' => ['nullable', 'uuid'],
            'plan' => ['required', 'array'],
            'plan.event' => ['required', 'string', 'max:160'],
            'plan.sim' => ['required', 'in:iRacing,LMU'],
            'plan.car' => ['required', 'string', 'max:160'],
            'plan.class' => ['nullable', 'string', 'max:80'],
            'plan.track' => ['required', 'string', 'max:160'],
            'plan.race_date' => ['required', 'date_format:Y-m-d'],
            'plan.start_time' => ['required', 'date_format:H:i'],
            'plan.race_mins' => ['required', 'numeric', 'between:1,10080'],
            'plan.lap_secs' => ['required', 'numeric', 'between:1,3600'],
            'plan.fuel_per_lap' => ['required', 'numeric', 'gt:0'],
            'plan.fuel_unit' => ['required', 'string', 'max:12'],
            'plan.capacity' => ['required', 'numeric', 'gt:0'],
            'plan.drivers' => ['required', 'array', 'max:20'],
            'plan.drivers.*' => ['nullable', 'string', 'max:120'],
            'plan.availability' => ['sometimes', 'array', 'max:100'],
            'plan.availability.*.driver' => ['required', 'string', 'max:120'],
            'plan.availability.*.from' => ['required', 'date'],
            'plan.availability.*.to' => ['required', 'date', 'after:plan.availability.*.from'],
            'plan.availability.*.from_label' => ['required', 'string', 'max:40'],
            'plan.availability.*.to_label' => ['required', 'string', 'max:40'],
            'plan.schedule' => ['required', 'array', 'max:60'],
            'plan.schedule.*.stint' => ['required', 'integer', 'between:1,60'],
            'plan.schedule.*.driver' => ['nullable', 'string', 'max:120'],
            'plan.schedule.*.standby' => ['nullable', 'string', 'max:120'],
            'plan.schedule.*.start' => ['required', 'date'],
            'plan.schedule.*.end' => ['required', 'date'],
            'plan.schedule.*.start_label' => ['required', 'string', 'max:40'],
            'plan.schedule.*.end_label' => ['required', 'string', 'max:40'],
            'plan.schedule.*.drive_mins' => ['required', 'numeric', 'min:0'],
            'plan.schedule.*.laps' => ['required', 'integer', 'min:0'],
            'plan.schedule.*.fuel' => ['required', 'numeric', 'min:0'],
            'plan.schedule.*.start_target' => ['required', 'numeric', 'min:0'],
            'plan.schedule.*.availability_status' => ['sometimes', 'in:Available,Conflict,Unassigned'],
            'plan.schedule.*.notes' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
