<?php

namespace App\Http\Controllers;

use App\Models\DriverApplication;
use App\Models\SponsorEnquiry;
use App\Services\DiscordNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EnquiryController extends Controller
{
    public function __construct(private readonly DiscordNotifier $discord) {}

    public function driver(Request $request): RedirectResponse
    {
        $this->guardAgainstSpam($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'country' => ['required', 'string', 'max:100'],
            'timezone' => ['required', 'string', 'max:100'],
            'age' => ['nullable', 'integer', 'between:16,99'],
            'discord' => ['required', 'string', 'max:100'],
            'simulators' => ['required', 'array', 'min:1'],
            'simulators.*' => ['in:iRacing,Le Mans Ultimate'],
            'car_class' => ['required', 'string', 'max:100'],
            'experience' => ['required', 'string', 'min:30', 'max:3000'],
            'availability' => ['required', 'string', 'min:10', 'max:1000'],
            'motivation' => ['required', 'string', 'min:30', 'max:3000'],
            'profile_links' => ['nullable', 'string', 'max:1000'],
        ]);

        $application = DriverApplication::create($data + ['ip_address' => $request->ip()]);
        $this->notify('New driver application: '.$application->name, $application->toArray());
        $this->discord->driver($application);

        return redirect()->route('enquiry.thanks', 'driver');
    }

    public function sponsor(Request $request): RedirectResponse
    {
        $this->guardAgainstSpam($request);

        $data = $request->validate([
            'company' => ['required', 'string', 'max:160'],
            'contact_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'company_website' => ['nullable', 'url:http,https', 'max:255'],
            'partnership_type' => ['required', 'in:Sponsorship,Product partnership,Affiliate partnership,Event collaboration,Other'],
            'budget' => ['nullable', 'string', 'max:100'],
            'goals' => ['required', 'string', 'min:20', 'max:2000'],
            'message' => ['required', 'string', 'min:20', 'max:3000'],
        ]);

        $data['website'] = $data['company_website'] ?? null;
        unset($data['company_website']);
        $enquiry = SponsorEnquiry::create($data + ['ip_address' => $request->ip()]);
        $this->notify('New partnership enquiry: '.$enquiry->company, $enquiry->toArray());
        $this->discord->sponsor($enquiry);

        return redirect()->route('enquiry.thanks', 'sponsor');
    }

    private function guardAgainstSpam(Request $request): void
    {
        $request->validate([
            'website' => ['nullable', 'size:0'],
            'started_at' => ['required', 'integer', 'lte:'.(now()->timestamp - 2)],
        ], ['website.size' => 'Unable to submit this form.', 'started_at.lte' => 'Please wait a moment before submitting.']);
    }

    private function notify(string $subject, array $data): void
    {
        $to = config('services.enquiries.to');
        if (! $to) {
            return;
        }

        Mail::raw(collect($data)->except(['ip_address'])->map(fn ($value, $key) => ucfirst(str_replace('_', ' ', $key)).': '.(is_array($value) ? implode(', ', $value) : $value))->implode("\n"), fn ($mail) => $mail->to($to)->subject($subject));
    }
}
