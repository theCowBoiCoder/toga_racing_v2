<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Driver Availability | Toga Racing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stint-availability.css') }}">
</head>
<body>
<header class="availability-header"><a href="{{ route('home') }}"><img src="{{ asset('images/logo.png') }}" alt=""><span>TOGA RACING</span></a><strong>DRIVER AVAILABILITY</strong></header>

<main id="availability-app" data-save-url="{{ route('stint-availability.save', $stintPlan->availability_token) }}">
    <section class="availability-hero">
        <span class="kicker">ENDURANCE OPERATIONS</span>
        <h1>{{ $stintPlan->plan['event'] ?? 'Race plan' }}</h1>
        <p>{{ $stintPlan->plan['sim'] ?? '' }} · {{ $stintPlan->plan['car'] ?? '' }} · {{ $stintPlan->plan['track'] ?? '' }}</p>
        <div class="race-time"><small>RACE START</small><strong>{{ $stintPlan->plan['race_date'] ?? '' }} · {{ $stintPlan->plan['start_time'] ?? '' }} local</strong><span>{{ $stintPlan->plan['race_mins'] ?? 0 }} minute race</span></div>
    </section>

    <section class="availability-card">
        <div class="card-heading"><span>01</span><div><small>YOUR RESPONSE</small><h2>When can you drive?</h2></div></div>
        <p class="intro">Select your name and enter every period when you are free. You can return to this link and update your response later.</p>
        <label class="driver-picker">Driver name<select id="driver-select"></select></label>

        <div class="response-state" id="response-state"></div>
        <div class="window-list" id="window-list"></div>

        <div class="availability-buttons">
            <button type="button" class="availability-button" id="whole-race">Available for whole race</button>
            <button type="button" class="availability-button" id="add-window">+ Add another time window</button>
            <button type="button" class="availability-button warning" id="unavailable">Not available for this race</button>
        </div>
        <button type="button" class="save-button" id="save-availability">Save my availability</button>
        <p class="save-message" id="save-message" aria-live="polite">Your response is shared with the race planner after you save.</p>
    </section>
</main>

<script id="availability-data" type="application/json">{!! json_encode($stintPlan->plan, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<script src="{{ asset('js/stint-availability.js') }}" defer></script>
</body>
</html>
