<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    private string $testImage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testImage = public_path('images/gallery/gallery-delete-test.jpg');
        File::put($this->testImage, 'test image');
    }

    protected function tearDown(): void
    {
        File::delete($this->testImage);

        parent::tearDown();
    }

    public function test_gallery_only_shows_delete_controls_to_the_configured_discord_admin(): void
    {
        config()->set('services.discord.results_admin_user_id', 'admin-123');

        $this->get('/gallery')
            ->assertOk()
            ->assertSee('gallery-delete-test.jpg')
            ->assertDontSee('Delete image');

        $this->withSession(['discord_user' => [
            'id' => 'admin-123',
            'username' => 'galleryadmin',
            'display_name' => 'Gallery Admin',
        ]])->get('/gallery')
            ->assertOk()
            ->assertSee('Delete image')
            ->assertSee('Gallery Admin');
    }

    public function test_only_the_configured_discord_admin_can_delete_a_gallery_image(): void
    {
        config()->set('services.discord.results_admin_user_id', 'admin-123');

        $this->withSession(['discord_user' => ['id' => 'someone-else']])
            ->delete(route('gallery.destroy', basename($this->testImage)))
            ->assertForbidden();

        $this->assertFileExists($this->testImage);

        $this->withSession(['discord_user' => ['id' => 'admin-123']])
            ->delete(route('gallery.destroy', basename($this->testImage)))
            ->assertRedirect(route('gallery'))
            ->assertSessionHas('gallery_image_deleted');

        $this->assertFileDoesNotExist($this->testImage);
    }
}
