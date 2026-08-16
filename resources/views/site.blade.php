<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>{{ ($page === 'home' ? 'Toga Racing | Precision in Every Turn' : ucfirst($page).' | Toga Racing') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}?v={{ filemtime(public_path('css/theme.css')) }}">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZHYM4ZLELB"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-ZHYM4ZLELB');
    </script>
</head>
<body>
<header class="header"><a class="brand" href="{{ route('home') }}"><img src="{{ asset('images/logo.png') }}" alt=""><span>TOGA RACING</span></a><button class="menu" aria-label="Toggle menu">☰</button><nav><a href="{{ route('stint-planner') }}">Stint Planner</a><a href="{{ route('results') }}">Results</a><a href="{{ route('gallery') }}">Gallery</a><a href="{{ route('news') }}">News</a><a href="{{ route('join') }}">Join us</a><a href="{{ route('partners') }}">Partner</a><a class="social" href="https://www.youtube.com/@TogaRacing">YouTube ↗</a></nav></header>

@if($page === 'home')
<main>
 <section class="hero"><div class="hero-copy"><h1 class="hero-title">TOGA <em>RACING.</em></h1><p class="hero-tagline">Precision in every turn.</p><p>Built on pace. Driven by teamwork. Competing together on the virtual grid.</p><div class="actions"><a class="button" href="{{ route('gallery') }}">View gallery →</a><a class="button ghost" href="{{ route('news') }}">Latest news</a></div></div></section>
 <section class="section sims-section"><div class="section-head"><div><span class="kicker">WHERE WE RACE</span><h2>SUPPORTED SIMULATORS</h2><p>Endurance competition across two leading racing platforms.</p></div></div><div class="sim-grid"><article class="sim-card iracing"><div><span>01 · COMPETITION</span><h3>iRACING</h3><p>GT, prototype and endurance racing with structured online competition and serious team preparation.</p><a href="{{ route('join') }}">Race with us →</a></div></article><article class="sim-card lmu"><div><span>02 · ENDURANCE</span><h3>LE MANS ULTIMATE</h3><p>Hypercar, LMP2 and GT racing inspired by the FIA World Endurance Championship.</p><a href="{{ route('join') }}">Express interest →</a></div></article></div></section>
 <section class="section instagram-section"><div class="section-head"><div><span class="kicker">FOLLOW THE ACTION</span><h2>ON INSTAGRAM</h2><p>Latest posts from @toga_racing.</p></div><a target="_blank" rel="noopener" href="{{ config('services.instagram.profile_url') }}">Follow us ↗</a></div><div class="instagram-grid">@forelse($instagramPosts as $post)<a class="instagram-card" target="_blank" rel="noopener" href="{{ $post['url'] }}"><img src="{{ $post['image'] }}" alt="{{ $post['title'] }}" loading="lazy"><span><b>◉ @toga_racing</b><small>{{ Str::limit($post['title'], 72) }}</small></span></a>@empty<a class="instagram-empty" target="_blank" rel="noopener" href="{{ config('services.instagram.profile_url') }}"><b>@toga_racing</b><span>Our latest racing photos and updates are on Instagram.</span><strong>VIEW INSTAGRAM ↗</strong></a>@endforelse</div></section>
 <section class="statement"><span class="kicker">ONE TEAM · ONE TARGET</span><h2>PACE IS PERSONAL.<br><em>PROGRESS IS SHARED.</em></h2><p>We race to compete, learn and move forward together—on every circuit, in every split.</p></section>
 <section class="section sponsor-section" id="sponsors"><div class="section-head"><div><span class="kicker">OFFICIAL PARTNERS</span><h2>BUILT FOR MORE PACE.</h2><p>Trusted tools and coaching for drivers who want to improve.</p></div></div><div class="sponsor-grid"><a class="sponsor-card" href="https://coachdaveacademy.com/?ref=toga" target="_blank" rel="sponsored noopener"><div class="sponsor-logo"><img src="{{ asset('images/sponsors/coach-dave-academy.png') }}" alt="Coach Dave Academy"></div><div class="sponsor-copy"><span>SIM RACING COACHING &amp; SETUPS</span><h3>COACH DAVE ACADEMY</h3><p>Professional coaching, data-driven tools and race-ready setups across leading simulators.</p><div class="discount"><small>5% OFF WITH CODE</small><b>toga5</b></div><strong>Visit Coach Dave →</strong></div></a><a class="sponsor-card" href="https://gosetups.gg/?ref=TOGA" target="_blank" rel="sponsored noopener"><div class="sponsor-logo"><img src="{{ asset('images/sponsors/go-setups.png') }}" alt="Go Setups"></div><div class="sponsor-copy"><span>COMPETITIVE SIM RACING SETUPS</span><h3>GO SETUPS</h3><p>High-performance setups designed to help you find consistency, confidence and lap time.</p><div class="discount"><small>5% OFF WITH CODE</small><b>toga5</b></div><strong>Visit Go Setups →</strong></div></a></div><p class="sponsor-disclosure">These are affiliate links. Toga Racing may receive support when you purchase through them, at no extra cost to you.</p></section>
 <section class="section pathways"><div class="path-card driver-path"><span class="kicker">DRIVE WITH TOGA</span><h2>READY TO JOIN THE GRID?</h2><p>We want reliable, respectful racers who value preparation, teamwork and long-term progress.</p><ul><li>iRacing and LMU opportunities</li><li>GT and prototype competition</li><li>A focused, supportive team environment</li></ul><a class="button" href="{{ route('join') }}">Driver application →</a></div><div class="path-card partner-path"><span class="kicker">BUILD WITH US</span><h2>PARTNER WITH TOGA.</h2><p>Put your brand alongside an ambitious sim racing team through liveries, content, broadcasts and events.</p><ul><li>Team and livery branding</li><li>Social media collaboration</li><li>Product and event partnerships</li></ul><a class="button ghost" href="{{ route('partners') }}">Partnership enquiry →</a></div></section>
 <section class="section"><div class="section-head"><div><span class="kicker">FROM THE PADDOCK</span><h2>LATEST NEWS</h2><p>Stay up to date with Toga Racing.</p></div><a href="{{ route('news') }}">All stories →</a></div>@include('partials.news')</section>
</main>
@elseif($page === 'join')
<main><section class="form-hero"><span class="kicker">DRIVE WITH TOGA</span><h1>JOIN THE <em>TEAM.</em></h1><p>Tell us about your racing, availability and what you can bring to Toga.</p></section><section class="form-shell"><div class="form-intro"><h2>DRIVER APPLICATION</h2><p>We review every application. Be honest about your experience—we care as much about attitude and reliability as outright pace.</p><div class="form-note"><b>Before you apply</b><span>You must be at least 16 and have an active Discord account.</span></div></div><form method="post" action="{{ route('join.submit') }}" class="enquiry-form">@csrf @include('partials.form-errors')<input class="form-trap" tabindex="-1" autocomplete="off" name="website"><input type="hidden" name="started_at" value="{{ now()->timestamp }}"><div class="field-grid"><label>Full name<input name="name" value="{{ old('name') }}" required></label><label>Email address<input type="email" name="email" value="{{ old('email') }}" required></label><label>Country<input name="country" value="{{ old('country') }}" required></label><label>Time zone<input name="timezone" value="{{ old('timezone') }}" placeholder="e.g. Europe/London" required></label><label>Age<input type="number" min="16" max="99" name="age" value="{{ old('age') }}" required></label><label>Discord username<input name="discord" value="{{ old('discord') }}" required></label></div><fieldset><legend>Simulator experience</legend><label class="check"><input type="checkbox" name="simulators[]" value="iRacing" @checked(in_array('iRacing', old('simulators', [])))> iRacing</label><label class="check"><input type="checkbox" name="simulators[]" value="Le Mans Ultimate" @checked(in_array('Le Mans Ultimate', old('simulators', [])))> Le Mans Ultimate</label><label class="check"><input type="checkbox" name="simulators[]" value="Assetto Corsa Competizione" @checked(in_array('Assetto Corsa Competizione', old('simulators', [])))> Assetto Corsa Competizione</label></fieldset><label>Preferred car class<input name="car_class" value="{{ old('car_class') }}" placeholder="GT3, Hypercar, LMP2…" required></label><label>Racing experience<textarea name="experience" rows="5" required>{{ old('experience') }}</textarea></label><label>Weekly availability<textarea name="availability" rows="3" required>{{ old('availability') }}</textarea></label><label>Why do you want to join Toga?<textarea name="motivation" rows="5" required>{{ old('motivation') }}</textarea></label><label>Racing profile links <small>Optional</small><textarea name="profile_links" rows="2">{{ old('profile_links') }}</textarea></label><button class="button" type="submit">Send application →</button></form></section></main>
@elseif($page === 'partners')
<main><section class="form-hero"><span class="kicker">BUILD WITH US</span><h1>PARTNER WITH <em>TOGA.</em></h1><p>Let’s create a partnership that puts your brand at the heart of competitive sim racing.</p></section><section class="form-shell"><div class="form-intro"><h2>PARTNERSHIP ENQUIRY</h2><p>From livery placement and social content to product testing and event collaboration, we are open to building something valuable together.</p><div class="form-note"><b>What we offer</b><span>Brand exposure, authentic racing content and access to a growing motorsport community.</span></div></div><form method="post" action="{{ route('partners.submit') }}" class="enquiry-form">@csrf @include('partials.form-errors')<input class="form-trap" tabindex="-1" autocomplete="off" name="website"><input type="hidden" name="started_at" value="{{ now()->timestamp }}"><div class="field-grid"><label>Company<input name="company" value="{{ old('company') }}" required></label><label>Contact name<input name="contact_name" value="{{ old('contact_name') }}" required></label><label>Email address<input type="email" name="email" value="{{ old('email') }}" required></label><label>Company website <small>Optional</small><input type="url" name="company_website" value="{{ old('company_website') }}"></label><label>Partnership type<select name="partnership_type" required><option value="">Choose an option</option>@foreach(['Sponsorship','Product partnership','Affiliate partnership','Event collaboration','Other'] as $option)<option @selected(old('partnership_type') === $option)>{{ $option }}</option>@endforeach</select></label><label>Indicative budget <small>Optional</small><input name="budget" value="{{ old('budget') }}" placeholder="e.g. Product support"></label></div><label>What would you like to achieve?<textarea name="goals" rows="4" required>{{ old('goals') }}</textarea></label><label>Tell us about the opportunity<textarea name="message" rows="6" required>{{ old('message') }}</textarea></label><button class="button" type="submit">Send enquiry →</button></form></section></main>
@elseif($page === 'results')
<main>
    <section class="page-hero results-hero"><span class="kicker">FROM THE CHEQUERED FLAG</span><h1>RACE <em>RESULTS.</em></h1><p>Approved Toga Racing results from iRacing, Le Mans Ultimate and Assetto Corsa Competizione.</p><a class="button" href="#submit-result">Submit a result →</a></section>
    <section class="section results-section"><div class="section-head"><div><span class="kicker">LATEST FINISHES</span><h2>THE SCOREBOARD</h2><p>Every result below has been reviewed by a Toga Racing admin.</p></div></div>@if(session('result_deleted'))<div class="result-alert">{{ session('result_deleted') }}</div>@endif<div class="result-grid">@forelse($raceResults as $result)<article class="result-card"><img src="{{ route('results.image', $result) }}" alt="Toga Racing car at {{ $result->event_name }}" loading="lazy"><div class="result-copy"><div class="result-meta"><span>{{ $result->event_date?->format('j M Y') ?? 'Date unavailable' }}</span><span>{{ $result->simulator }}</span><span>Split {{ $result->split_number }}</span><span>{{ $result->car_class }}</span></div><h3>{{ $result->event_name }}</h3><div class="result-positions"><div><small>STARTED</small><b>P{{ $result->starting_position }}</b></div><span>→</span><div><small>FINISHED</small><b>P{{ $result->finishing_position }}</b></div></div><div class="result-team"><small>TEAM MEMBERS</small><p>{!! nl2br(e($result->team_members)) !!}</p></div>@if(hash_equals((string) config('services.discord.results_admin_user_id'), (string) session('discord_user.id', '')))<form method="post" action="{{ route('results.destroy', $result) }}" class="result-delete-form" onsubmit="return confirm('Delete this race result permanently?')">@csrf @method('DELETE')<button type="submit">Delete result</button></form>@endif</div></article>@empty<div class="results-empty"><b>THE GRID IS READY.</b><p>No approved results have been published yet. Submit the first one below.</p></div>@endforelse</div></section>
    @include('partials.race-result-submission')
</main>
@elseif($page === 'thanks')
<main><section class="thanks"><span class="success-mark">✓</span><span class="kicker">{{ $enquiryType === 'race-result' ? 'RESULT RECEIVED' : 'ENQUIRY RECEIVED' }}</span><h1>THANKS FOR<br><em>{{ $enquiryType === 'race-result' ? 'SUBMITTING.' : 'REACHING OUT.' }}</em></h1><p>{{ $enquiryType === 'driver' ? 'Your driver application is safely with us. We’ll review it and contact you using the email or Discord details supplied.' : ($enquiryType === 'race-result' ? 'Your race result is safely with us. A Toga Racing admin will review it in Discord before it appears on the website.' : 'Your partnership enquiry is safely with us. We’ll review the opportunity and reply using the email address supplied.') }}</p><a class="button" href="{{ $enquiryType === 'race-result' ? route('results') : route('home') }}">{{ $enquiryType === 'race-result' ? 'View race results' : 'Return home' }} →</a></section></main>
@elseif($page === 'gallery')
<main>
    <section class="page-hero gallery-hero">
        <span class="kicker">TRACKSIDE</span><h1>RACING <em>GALLERY.</em></h1><p>Moments from our races across iRacing and Le Mans Ultimate.</p>
        @include('partials.form-errors')
        @php
            $galleryAdminId = (string) config('services.discord.results_admin_user_id', '');
            $isGalleryAdmin = $galleryAdminId !== '' && hash_equals($galleryAdminId, (string) session('discord_user.id', ''));
        @endphp
        @if($isGalleryAdmin)
            <p class="gallery-admin-status">Signed in as {{ session('discord_user.display_name') }}. Gallery management is enabled.</p>
            <form method="post" action="{{ route('discord.logout', ['return' => 'gallery']) }}" class="discord-logout gallery-logout">@csrf<button type="submit">Sign out of gallery management</button></form>
        @elseif(!session('discord_user.id'))
            <a class="button ghost gallery-admin-login" href="{{ route('discord.login', ['return' => 'gallery']) }}">Admin sign in with Discord</a>
        @endif
    </section>
    @if($isGalleryAdmin)
        <section class="gallery-upload-panel">
            <div><span class="kicker">GALLERY MANAGEMENT</span><h2>UPLOAD AN IMAGE</h2><p>JPG, PNG or WebP · maximum 10 MB</p></div>
            <form method="post" action="{{ route('gallery.store') }}" enctype="multipart/form-data" class="gallery-upload-form">
                @csrf
                <label><span>Select image</span><input type="file" name="gallery_image" accept="image/jpeg,image/png,image/webp" required></label>
                <button class="button" type="submit">Upload image →</button>
            </form>
        </section>
    @endif
    @if(session('gallery_image_deleted') || session('gallery_image_uploaded'))<div class="result-alert gallery-alert">{{ session('gallery_image_deleted') ?: session('gallery_image_uploaded') }}</div>@endif
    <section class="gallery">
        @forelse($galleryImages as $image)
            <figure>
                <img src="{{ asset('images/gallery/'.$image) }}" alt="Toga Racing on track" loading="lazy">
                @if($isGalleryAdmin)
                    <form method="post" action="{{ route('gallery.destroy', $image) }}" class="gallery-delete-form" onsubmit="return confirm('Delete this gallery image permanently?')">
                        @csrf @method('DELETE')
                        <button type="submit">Delete image</button>
                    </form>
                @endif
            </figure>
        @empty
            <p class="gallery-empty">No gallery images are currently available.</p>
        @endforelse
    </section>
</main>
@elseif($page === 'news')
<main><section class="page-hero"><span class="kicker">FROM THE PADDOCK</span><h1>LATEST <em>NEWS.</em></h1></section><section class="section">@include('partials.news')</section></main>
@else
<main><article class="article"><span class="kicker">{{ $article['date'] }} · TEAM NEWS</span><h1>{{ $article['title'] }}</h1><p class="lead">{{ $article['excerpt'] }}</p><div class="article-copy"><p>{{ $article['body'] }}</p></div><a class="button ghost" href="{{ route('news') }}">← All stories</a></article></main>
@endif
<footer><div><span class="footer-logo">TOGA RACING</span><p>Precision in Every Turn</p></div><div><a href="{{ route('stint-planner') }}">Stint planner</a><a href="{{ route('results') }}">Results</a><a href="{{ route('gallery') }}">Gallery</a><a href="{{ route('news') }}">News</a><a href="{{ route('join') }}">Join us</a><a href="{{ route('partners') }}">Partners</a></div><small>© {{ date('Y') }} Toga Racing. All rights reserved.</small></footer>
<script>
document.querySelector('.menu').onclick=()=>document.querySelector('nav').classList.toggle('open');
</script>
</body></html>
