<section class="form-shell result-form-shell" id="submit-result">
    <div class="form-intro">
        <span class="kicker">ADD TO THE SCOREBOARD</span>
        <h2>SUBMIT A RESULT</h2>
        <p>Share the race details and a photo of the car. The result remains private until a Toga Racing admin approves it in Discord.</p>
        <div class="form-note">
            <b>Discord verified</b>
            <span>Sign in with Discord before submitting. Your verified Discord identity is attached automatically for the reviewing admin.</span>
        </div>
    </div>

    @if(session('discord_user.id'))
        <div class="result-submit-panel">
            <div class="discord-auth-bar">
                <div>
                    <small>SIGNED IN WITH DISCORD</small>
                    <b>{{ session('discord_user.display_name') }}</b>
                    <span>&#64;{{ session('discord_user.username') }} · {{ session('discord_user.id') }}</span>
                </div>
            </div>
            <form id="race-result-form" method="post" action="{{ route('results.submit') }}" enctype="multipart/form-data" class="enquiry-form">
                @csrf
                @include('partials.form-errors')
                <input class="form-trap" tabindex="-1" autocomplete="off" name="website">
                <input type="hidden" name="started_at" value="{{ now()->timestamp }}">
                <div class="field-grid">
                    <label>Event name<input name="event_name" value="{{ old('event_name') }}" placeholder="e.g. 24 Hours of Spa" required></label>
                    <label>Event date<input type="date" name="event_date" value="{{ old('event_date') }}" max="{{ now()->toDateString() }}" required></label>
                    <label>Simulator<select name="simulator" required><option value="">Choose a simulator</option>@foreach(['iRacing','Le Mans Ultimate'] as $simulator)<option value="{{ $simulator }}" @selected(old('simulator') === $simulator)>{{ $simulator }}</option>@endforeach</select></label>
                    <label>Split number<input type="number" min="1" max="999" name="split_number" value="{{ old('split_number') }}" required></label>
                    <label>Car class<input name="car_class" value="{{ old('car_class') }}" placeholder="e.g. GT3, Hypercar, LMP2" required></label>
                    <label>Starting position<input type="number" min="1" max="999" name="starting_position" value="{{ old('starting_position') }}" required></label>
                    <label>Finishing position<input type="number" min="1" max="999" name="finishing_position" value="{{ old('finishing_position') }}" required></label>
                </div>
                <label>Team members<textarea name="team_members" rows="4" placeholder="One driver per line" required>{{ old('team_members') }}</textarea></label>
                <label>Car image <small>JPG, PNG or WebP · maximum 10 MB</small><input type="file" name="car_image" accept="image/jpeg,image/png,image/webp" required></label>
            </form>
            <div class="result-form-actions">
                <button class="button result-submit-button" type="submit" form="race-result-form">Submit result →</button>
                <form method="post" action="{{ route('discord.logout') }}" class="discord-logout">
                    @csrf
                    <button type="submit">Sign out</button>
                </form>
            </div>
        </div>
    @else
        <div class="discord-login-card">
            @include('partials.form-errors')
            <span class="discord-mark">DISCORD</span>
            <h3>SIGN IN TO SUBMIT</h3>
            <p>We use Discord to verify who sent each result. Only your Discord ID, username and display name are saved with the submission.</p>
            <a class="button discord-button" href="{{ route('discord.login') }}">Continue with Discord →</a>
        </div>
    @endif
</section>
