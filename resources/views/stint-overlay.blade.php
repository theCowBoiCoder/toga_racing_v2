<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ data_get($stintPlan->plan, 'event', 'Toga Racing') }} Overlay</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stint-overlay.css') }}">
</head>
<body class="overlay-mode-{{ $mode }}">
<main id="overlay" data-endpoint="{{ route('stint-overlay.data', $stintPlan->token) }}">
    <section class="overlay-brand">
        <img src="{{ asset('images/logo.png') }}" alt="">
        <div><small>TOGA RACING · LIVE STRATEGY</small><h1 id="overlay-event">LOADING PLAN</h1><p id="overlay-meta">Connecting…</p></div>
    </section>
    <section class="race-clock"><small id="clock-label">RACE CLOCK</small><strong id="race-clock">--:--:--</strong><span id="sync-state">SYNCING</span></section>
    <section class="current-stint">
        <span class="stint-index" id="current-index">—</span>
        <div><small id="current-label">CURRENT DRIVER</small><strong id="current-driver">TBC</strong><span id="current-time">—</span></div>
        <dl><div><dt>LAPS</dt><dd id="current-laps">—</dd></div><div><dt>FUEL TARGET</dt><dd id="current-fuel">—</dd></div></dl>
    </section>
    <section class="upcoming"><small>UPCOMING STINTS</small><div id="upcoming-stints"></div></section>
</main>
<script src="{{ asset('js/stint-overlay.js') }}" defer></script>
</body>
</html>
