<?php

use Illuminate\Support\Facades\Route;
use App\Services\InstagramFeed;
use App\Http\Controllers\EnquiryController;

$drivers = fn () => collect([
    ['slug' => 'hayden-sweet', 'name' => 'Hayden Sweet', 'number' => '29', 'role' => 'Team Manager', 'country' => 'United Kingdom', 'image' => null, 'bio' => 'Father of two by day and sim racer by night. A GT3 specialist chasing precision, clean overtakes and the perfect lap.'],
    ['slug' => 'stijn-donckerwolke', 'name' => 'Stijn Donckerwolke', 'number' => '51', 'role' => 'Esports Driver', 'country' => 'Belgium', 'image' => null, 'bio' => 'Stijn’s two passions are sim racing and cycling.'],
    ['slug' => 'mitchell-sterrenberg', 'name' => 'Mitchell Sterrenberg', 'number' => '27', 'role' => 'Team Manager', 'country' => 'Netherlands', 'image' => null, 'bio' => 'A software developer and racer who feels at home in GT3 and LMP2 machinery—with a soft spot for Japanese cars.'],
    ['slug' => 'lukas-james', 'name' => 'Lukas James', 'number' => '17', 'role' => 'Esports Driver', 'country' => 'USA', 'image' => null, 'bio' => 'Toga Racing esports driver, competing as car number 17.'],
    ['slug' => 'konrad-wasowicz', 'name' => 'Konrad Wasowicz', 'number' => '12', 'role' => 'Academy Driver', 'country' => 'Poland', 'image' => null, 'bio' => 'An academy driver balancing school and racing, with a focus on GT3 and Hypercars in iRacing.'],
    ['slug' => 'lukas-kuthe', 'name' => 'Lukas Küthe', 'number' => '63', 'role' => 'Academy Driver', 'country' => 'Austria', 'image' => null, 'bio' => 'The team’s Austrian academy driver, focused on improving and achieving ambitious goals.'],
    ['slug' => 'troy-fraser-mcgonigal', 'name' => 'Troy-Fraser McGonigal', 'number' => '—', 'role' => 'Racing Driver', 'country' => 'United Kingdom', 'image' => null, 'bio' => 'Serving soldier, Army karter, guitar player and Oasis fan. If you aren’t first, you’re last.'],
    ['slug' => 'jordan-mcgonigal', 'name' => 'Jordan McGonigal', 'number' => '—', 'role' => 'Racing Driver', 'country' => 'Scotland', 'image' => null, 'bio' => 'A lifelong motorsport fan with 15 years in sim racing, at his best around Spa, Bathurst, Le Mans and the Nordschleife.'],
]);

$articles = fn () => collect([
    ['slug' => 'sim-racing-on-a-budget', 'date' => '28 February 2026', 'title' => 'Sim Racing on a Budget: How to Build a Pro-Level Rig Without the Pro-Level Debt', 'excerpt' => 'Ready to trade your controller for a steering wheel? Build a capable rig without emptying your bank account.', 'body' => 'A competitive setup starts with the fundamentals: a dependable force-feedback wheel, stable pedals and a seating position you can repeat every lap. Buy the parts that affect consistency first, then upgrade screens, cosmetics and accessories over time.'],
    ['slug' => 'esports-racing', 'date' => '28 February 2026', 'title' => 'Esports Racing: More Than Just “Playing Games” in Your Pants', 'excerpt' => 'Sim racing asks for the same focus, preparation and teamwork found throughout competitive motorsport.', 'body' => 'At team level, esports racing means preparation, telemetry, strategy and trust. Drivers practise together, study data and learn how to deliver under pressure—skills that make every race more rewarding.'],
]);

Route::get('/', fn (InstagramFeed $instagram) => view('site', ['page' => 'home', 'drivers' => $drivers(), 'articles' => $articles(), 'instagramPosts' => $instagram->posts()]))->name('home');
Route::get('/join', fn () => view('site', ['page' => 'join']))->name('join');
Route::post('/join', [EnquiryController::class, 'driver'])->middleware('throttle:5,10')->name('join.submit');
Route::get('/partners', fn () => view('site', ['page' => 'partners']))->name('partners');
Route::post('/partners', [EnquiryController::class, 'sponsor'])->middleware('throttle:5,10')->name('partners.submit');
Route::get('/enquiry-received/{type}', fn (string $type) => in_array($type, ['driver', 'sponsor'], true)
    ? view('site', ['page' => 'thanks', 'enquiryType' => $type])
    : abort(404))->name('enquiry.thanks');
Route::get('/gallery', fn () => view('site', ['page' => 'gallery', 'drivers' => $drivers(), 'articles' => $articles()]))->name('gallery');
Route::get('/news', fn () => view('site', ['page' => 'news', 'drivers' => $drivers(), 'articles' => $articles()]))->name('news');
Route::get('/news/{slug}', function ($slug) use ($drivers, $articles) {
    return view('site', ['page' => 'article', 'article' => $articles()->firstWhere('slug', $slug) ?? abort(404), 'drivers' => $drivers(), 'articles' => $articles()]);
})->name('article');
