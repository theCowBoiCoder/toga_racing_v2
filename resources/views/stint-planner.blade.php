<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Plan Toga Racing endurance stints, fuel targets, drivers and race clock times for iRacing and Le Mans Ultimate.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>Stint Planner | Toga Racing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/stint-planner.css') }}">
</head>
<body class="planner-page">
<header class="header">
    <a class="brand" href="{{ route('home') }}"><img src="{{ asset('images/logo.png') }}" alt=""><span>TOGA RACING</span></a>
    <button class="menu" type="button" aria-label="Toggle menu" aria-expanded="false">☰</button>
    <nav><a class="active" href="{{ route('stint-planner') }}">Stint Planner</a><a href="{{ route('gallery') }}">Gallery</a><a href="{{ route('news') }}">News</a><a href="{{ route('join') }}">Join us</a><a href="{{ route('partners') }}">Partner</a></nav>
</header>

<main id="planner-app" data-template-url="{{ route('stint-planner.template') }}" data-publish-url="{{ route('stint-planner.publish') }}" data-discord-url="{{ route('stint-planner.discord') }}" data-availability-sync-url="{{ route('stint-planner.availability-sync') }}" data-share-configured="{{ config('services.stint_planner.share_key') ? 'true' : 'false' }}">
    <section class="planner-hero">
        <div>
            <span class="kicker">ENDURANCE OPERATIONS</span>
            <h1>BUILD THE<br><em>RACE PLAN.</em></h1>
            <p>Drivers, fuel and the clock in one clean stint sheet. Your work is stored in this browser automatically.</p>
        </div>
        <div class="hero-status" aria-live="polite">
            <span>PLAN STATUS</span><strong id="save-status">Ready</strong><small id="save-detail">Local browser storage</small>
        </div>
    </section>

    <section class="planner-shell">
        <div class="planner-toolbar" aria-label="Planner actions">
            <button class="planner-button primary" id="save-plan" type="button">Save locally</button>
            <button class="planner-button" id="export-csv" type="button">Export schedule CSV</button>
            <button class="planner-button" id="export-json" type="button">Download backup</button>
            <button class="planner-button" id="import-json" type="button">Restore backup</button>
            <input id="import-file" type="file" accept="application/json,.json" hidden>
            <a class="planner-button" href="{{ route('stint-planner.template') }}">Download Excel workbook</a>
            <button class="planner-button danger" id="reset-plan" type="button">Reset</button>
        </div>

        <div class="planner-layout">
            <aside class="planner-controls">
                <section class="planner-panel">
                    <div class="panel-heading"><span>01</span><div><small>RACE INPUTS</small><h2>Event setup</h2></div></div>
                    <div class="control-grid">
                        <label class="wide">Event name<input id="event" type="text" autocomplete="off"></label>
                        <label>Simulator<select id="sim"><option>iRacing</option><option>LMU</option></select></label>
                        <label>Car<select id="car"></select></label>
                        <label>Track<input id="track" type="text" autocomplete="off"></label>
                        <label>Class<input id="car-class" type="text" readonly></label>
                        <label>Race date<input id="race-date" type="date"></label>
                        <label>Local start time<input id="start-time" type="time"></label>
                        <label>Race length (mins)<input id="race-mins" type="number" min="1" step="1"></label>
                        <label>Average lap (secs)<input id="lap-secs" type="number" min="1" step="0.1"></label>
                        <label><span id="fuel-per-lap-label">Fuel per lap</span><input id="fuel-per-lap" type="number" min="0.001" step="0.01"></label>
                        <label>Sourced capacity<input id="reference-capacity" type="text" readonly></label>
                        <label>Usable capacity override<input id="usable-capacity" type="number" min="0" step="0.1"></label>
                        <label>Reserve laps<input id="reserve-laps" type="number" min="0" step="1"></label>
                        <label>Max stint (mins)<input id="max-stint" type="number" min="1" step="0.1"></label>
                        <label class="wide">Pit loss between stints (secs)<input id="pit-loss" type="number" min="0" step="1"></label>
                    </div>
                    <p class="source-note" id="capacity-note"></p>
                </section>

                <section class="planner-panel">
                    <div class="panel-heading"><span>02</span><div><small>TEAM</small><h2>Driver roster</h2></div></div>
                    <div class="roster-list" id="roster-list"></div>
                    <button class="text-button" id="add-driver" type="button">+ Add driver</button>
                </section>

                <section class="planner-panel availability-panel">
                    <div class="panel-heading"><span>03</span><div><small>DRIVER RESPONSES</small><h2>Availability</h2></div></div>
                    <p class="availability-help">Publish the plan, then send the separate availability link to the drivers. Refresh here after they respond.</p>
                    <div class="availability-summary" id="availability-summary"></div>
                    <div class="availability-actions">
                        <button class="planner-button" id="sync-availability" type="button">Refresh driver responses</button>
                        <button class="planner-button primary" id="auto-assign" type="button">Auto assign available drivers</button>
                    </div>
                    <p class="availability-message" id="availability-message" aria-live="polite"></p>
                </section>
            </aside>

            <div class="planner-output">
                <section class="summary-grid" aria-label="Race summary">
                    <article><small>RACE FINISH</small><strong id="summary-finish">—</strong><span id="summary-duration">—</span></article>
                    <article><small>PLANNED STINT</small><strong id="summary-stint">—</strong><span id="summary-laps-tank">—</span></article>
                    <article><small>ESTIMATED STINTS</small><strong id="summary-stints">—</strong><span id="summary-pit">—</span></article>
                    <article><small>RACE FUEL</small><strong id="summary-fuel">—</strong><span id="summary-laps">—</span></article>
                </section>

                <section class="planner-panel broadcast-panel">
                    <div class="broadcast-heading">
                        <div class="panel-heading"><span>LIVE</span><div><small>TEAM SHARE</small><h2>Discord &amp; OBS</h2></div></div>
                        <p>Publish the latest plan, copy either URL into an OBS Browser Source, or send the schedule to your team’s Discord channel.</p>
                    </div>
                    <div class="broadcast-actions">
                        <label>Team share key<input id="share-key" type="password" autocomplete="current-password" autocapitalize="none" spellcheck="false" placeholder="Required to publish or send"></label>
                        <button class="planner-button primary" id="publish-plan" type="button">Publish / update overlays</button>
                        <button class="planner-button" id="send-discord" type="button">Send plan to Discord</button>
                    </div>
                    <p class="share-message{{ config('services.stint_planner.share_key') ? '' : ' error' }}" id="share-message" aria-live="polite">{{ config('services.stint_planner.share_key') ? 'Publish once to generate your availability and OBS links.' : 'Setup required: STINT_PLANNER_SHARE_KEY is missing from the server environment.' }}</p>
                    <div class="overlay-links" id="overlay-links" hidden>
                        <label class="wide"><span>Driver availability form · send this link to the team</span><span class="copy-field"><input id="availability-form-url" type="url" readonly><button type="button" data-copy-overlay="availability-form-url">Copy</button></span></label>
                        <label><span>Compact overlay · 1920 × 180</span><span class="copy-field"><input id="compact-overlay-url" type="url" readonly><button type="button" data-copy-overlay="compact-overlay-url">Copy</button></span></label>
                        <label><span>Schedule overlay · 1920 × 360</span><span class="copy-field"><input id="schedule-overlay-url" type="url" readonly><button type="button" data-copy-overlay="schedule-overlay-url">Copy</button></span></label>
                    </div>
                </section>

                <section class="planner-panel schedule-panel">
                    <div class="schedule-heading">
                        <div class="panel-heading"><span>04</span><div><small>LIVE PLAN</small><h2>Stint schedule</h2></div></div>
                        <p>Assign drivers and stand-by cover. Availability conflicts are highlighted automatically.</p>
                    </div>
                    <div class="table-wrap"><table><thead><tr><th>Stint</th><th>Driver</th><th>Stand-by</th><th>Availability</th><th>Start</th><th>End</th><th>Mins</th><th>Laps</th><th>Fuel</th><th>Start target</th><th>Notes</th></tr></thead><tbody id="schedule-body"></tbody></table></div>
                    <div class="empty-state" id="empty-state" hidden>Enter valid race, lap and fuel values to build the schedule.</div>
                </section>

                <section class="planner-panel totals-panel">
                    <div class="panel-heading"><span>05</span><div><small>WORKLOAD</small><h2>Driver totals</h2></div></div>
                    <div class="driver-totals" id="driver-totals"></div>
                </section>
            </div>
        </div>
    </section>
</main>

<footer><div><span class="footer-logo">TOGA RACING</span><p>Precision in Every Turn</p></div><div><a href="{{ route('home') }}">Home</a><a href="{{ route('gallery') }}">Gallery</a><a href="{{ route('join') }}">Join us</a></div><small>© {{ date('Y') }} Toga Racing. All rights reserved.</small></footer>
<script src="{{ asset('js/stint-planner.js') }}?v={{ filemtime(public_path('js/stint-planner.js')) }}" defer></script>
</body>
</html>
