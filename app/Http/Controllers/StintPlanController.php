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
        $plan = $validated['plan'];

        if ($stintPlan) {
            $current = $stintPlan->plan;
            $drivers = $plan['drivers'] ?? [];
            $submitted = array_values(array_intersect($current['availability_submitted'] ?? [], $drivers));
            $plan['availability_submitted'] = $submitted;
            $plan['availability'] = collect($current['availability'] ?? [])->filter(
                fn (array $window) => in_array($window['driver'] ?? '', $submitted, true)
            )->values()->all();
            $stintPlan->update([
                'availability_token' => $stintPlan->availability_token ?: (string) Str::uuid(),
                'plan' => $plan,
            ]);
        } else {
            $plan['availability'] = [];
            $plan['availability_submitted'] = [];
            $stintPlan = StintPlan::create([
                'token' => (string) Str::uuid(),
                'availability_token' => (string) Str::uuid(),
                'plan' => $plan,
            ]);
        }

        return response()->json([
            'token' => $stintPlan->token,
            'updated_at' => $stintPlan->updated_at->toIso8601String(),
            'overlays' => [
                'compact' => route('stint-overlay', ['token' => $stintPlan->token, 'mode' => 'compact']),
                'schedule' => route('stint-overlay', ['token' => $stintPlan->token, 'mode' => 'schedule']),
            ],
            'availability_url' => route('stint-availability', $stintPlan->availability_token),
            'availability' => $stintPlan->plan['availability'] ?? [],
            'availability_submitted' => $stintPlan->plan['availability_submitted'] ?? [],
        ])->header('Cache-Control', 'no-store, private');
    }

    public function availability(string $token): View
    {
        $stintPlan = StintPlan::where('availability_token', $token)->firstOrFail();

        return view('stint-availability', ['stintPlan' => $stintPlan]);
    }

    public function saveAvailability(string $token, Request $request): JsonResponse
    {
        $stintPlan = StintPlan::where('availability_token', $token)->firstOrFail();
        $data = $request->validate([
            'driver' => ['required', 'string', 'max:120'],
            'windows' => ['present', 'array', 'max:12'],
            'windows.*.from' => ['required', 'date'],
            'windows.*.to' => ['required', 'date', 'after:windows.*.from'],
            'windows.*.from_label' => ['required', 'string', 'max:40'],
            'windows.*.to_label' => ['required', 'string', 'max:40'],
        ]);
        $plan = $stintPlan->plan;
        abort_unless(in_array($data['driver'], $plan['drivers'] ?? [], true), 422, 'Choose a driver from this race roster.');

        $availability = collect($plan['availability'] ?? [])->reject(fn (array $window) => ($window['driver'] ?? '') === $data['driver']);
        foreach ($data['windows'] as $window) {
            $availability->push(['driver' => $data['driver'], ...$window]);
        }
        $submitted = collect($plan['availability_submitted'] ?? [])->push($data['driver'])->unique()->values()->all();
        $plan['availability'] = $availability->values()->all();
        $plan['availability_submitted'] = $submitted;
        $stintPlan->update(['plan' => $plan]);

        return response()->json(['message' => 'Your availability has been saved.', 'availability' => $plan['availability'], 'availability_submitted' => $submitted]);
    }

    public function syncAvailability(Request $request): JsonResponse
    {
        $this->authorizeShare($request);
        $data = $request->validate(['plan_token' => ['required', 'uuid']]);
        $stintPlan = StintPlan::where('token', $data['plan_token'])->firstOrFail();
        $plan = $stintPlan->plan;

        return response()->json([
            'availability' => $plan['availability'] ?? [],
            'availability_submitted' => $plan['availability_submitted'] ?? [],
            'availability_url' => route('stint-availability', $stintPlan->availability_token),
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
        $configured = trim((string) config('services.stint_planner.share_key'));
        $provided = trim((string) $request->input('share_key'));

        abort_if($configured === '', 503, 'The stint planner share key is not configured.');
        abort_unless(hash_equals($configured, $provided), 403, 'The team share key is incorrect. Re-enter it without any extra characters.');
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
            'plan.availability_submitted' => ['sometimes', 'array', 'max:20'],
            'plan.availability_submitted.*' => ['string', 'max:120'],
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
