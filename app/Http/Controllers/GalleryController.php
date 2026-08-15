<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class GalleryController extends Controller
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    public function index(): View
    {
        $images = collect(File::files(public_path('images/gallery')))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), self::ALLOWED_EXTENSIONS, true))
            ->map(fn ($file) => $file->getFilename())
            ->sort(fn (string $left, string $right) => strnatcasecmp($left, $right))
            ->values();

        return view('site', ['page' => 'gallery', 'galleryImages' => $images]);
    }

    public function destroy(Request $request, string $image): RedirectResponse
    {
        $configuredAdminId = (string) config('services.discord.results_admin_user_id', '');
        $signedInUserId = (string) $request->session()->get('discord_user.id', '');

        abort_unless(
            $configuredAdminId !== ''
                && $signedInUserId !== ''
                && hash_equals($configuredAdminId, $signedInUserId),
            403,
        );

        abort_unless(
            basename($image) === $image
                && in_array(strtolower(pathinfo($image, PATHINFO_EXTENSION)), self::ALLOWED_EXTENSIONS, true),
            404,
        );

        $galleryDirectory = realpath(public_path('images/gallery'));
        $imagePath = realpath(public_path('images/gallery/'.$image));

        abort_unless(
            $galleryDirectory !== false
                && $imagePath !== false
                && dirname($imagePath) === $galleryDirectory
                && File::isFile($imagePath),
            404,
        );

        abort_unless(File::delete($imagePath), 500, 'The gallery image could not be deleted.');

        return redirect()->route('gallery')->with('gallery_image_deleted', 'Gallery image deleted.');
    }
}
