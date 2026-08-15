<?php

namespace App\Http\Controllers;

use App\Models\RaceResult;
use App\Services\DiscordNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RaceResultController extends Controller
{
    public function __construct(private readonly DiscordNotifier $discord) {}

    public function index(): View
    {
        return view('site', [
            'page' => 'results',
            'raceResults' => RaceResult::query()
                ->where('status', 'approved')
                ->latest('approved_at')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $request->session()->has('discord_user.id')) {
            return redirect()->route('discord.login');
        }

        $request->validate([
            'website' => ['nullable', 'size:0'],
            'started_at' => ['required', 'integer', 'lte:'.(now()->timestamp - 2)],
            'event_name' => ['required', 'string', 'max:180'],
            'event_date' => ['required', 'date', 'before_or_equal:today'],
            'simulator' => ['required', 'in:iRacing,Le Mans Ultimate'],
            'split_number' => ['required', 'integer', 'between:1,999'],
            'starting_position' => ['required', 'integer', 'between:1,999'],
            'finishing_position' => ['required', 'integer', 'between:1,999'],
            'car_class' => ['required', 'string', 'max:100'],
            'team_members' => ['required', 'string', 'min:2', 'max:2000'],
            'car_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ], [
            'website.size' => 'Unable to submit this form.',
            'started_at.lte' => 'Please wait a moment before submitting.',
            'car_image.max' => 'The car image must be no larger than 10 MB.',
        ]);

        $image = $request->file('car_image');
        $path = $image->store('race-results', 'local');

        $result = RaceResult::create([
            'event_name' => $request->string('event_name')->trim()->toString(),
            'event_date' => $request->date('event_date'),
            'simulator' => $request->string('simulator')->toString(),
            'split_number' => $request->integer('split_number'),
            'starting_position' => $request->integer('starting_position'),
            'finishing_position' => $request->integer('finishing_position'),
            'car_class' => $request->string('car_class')->trim()->toString(),
            'team_members' => $request->string('team_members')->trim()->toString(),
            'image_path' => $path,
            'image_original_name' => $image->getClientOriginalName(),
            'submitter_discord_id' => $request->session()->get('discord_user.id'),
            'submitter_discord_username' => $request->session()->get('discord_user.username'),
            'submitter_discord_display_name' => $request->session()->get('discord_user.display_name'),
            'ip_address' => $request->ip(),
        ]);

        $this->discord->raceResult($result);

        return redirect()->route('enquiry.thanks', 'race-result');
    }

    public function image(RaceResult $raceResult): StreamedResponse
    {
        abort_unless($raceResult->status === 'approved', 404);
        abort_unless(Storage::disk('local')->exists($raceResult->image_path), 404);

        return Storage::disk('local')->response(
            $raceResult->image_path,
            $raceResult->image_original_name,
            ['Cache-Control' => 'public, max-age=86400'],
        );
    }

    public function destroy(Request $request, RaceResult $raceResult): RedirectResponse
    {
        abort_unless(
            hash_equals(
                (string) config('services.discord.results_admin_user_id'),
                (string) $request->session()->get('discord_user.id', ''),
            ),
            403,
        );

        Storage::disk('local')->delete($raceResult->image_path);
        $raceResult->delete();

        return redirect()->route('results')->with('result_deleted', 'Race result deleted.');
    }
}
