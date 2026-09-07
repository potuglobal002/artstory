<?php

namespace Tests\Unit;

use App\Models\VirtualGallery;
use App\Models\VirtualGalleryRoom;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VirtualGalleryModelTest extends TestCase
{
    public function test_gallery_and_room_media_urls_use_the_public_disk(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('virtual-galleries/cover.webp', 'cover');
        Storage::disk('public')->put('virtual-galleries/room-panorama.webp', 'panorama');

        $gallery = new VirtualGallery(['cover_image_path' => 'virtual-galleries/cover.webp']);
        $room = new VirtualGalleryRoom(['panorama_path' => 'virtual-galleries/room-panorama.webp']);

        $this->assertSame(
            Storage::disk('public')->url('virtual-galleries/cover.webp'),
            $gallery->coverImageUrl(),
        );
        $this->assertSame(
            Storage::disk('public')->url('virtual-galleries/room-panorama.webp'),
            $room->panoramaUrl(),
        );
    }

    public function test_gallery_and_room_media_urls_are_null_without_an_upload(): void
    {
        $this->assertNull((new VirtualGallery)->coverImageUrl());
        $this->assertNull((new VirtualGalleryRoom)->panoramaUrl());
    }

    public function test_automatic_gallery_has_a_curated_environment_and_supports_an_admin_upload(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('virtual-galleries/environments/custom.webp', 'panorama');

        $gallery = new VirtualGallery(['automatic_use_realistic_environment' => true]);
        $customGallery = new VirtualGallery([
            'automatic_use_realistic_environment' => true,
            'automatic_environment_path' => 'virtual-galleries/environments/custom.webp',
        ]);
        $disabledGallery = new VirtualGallery(['automatic_use_realistic_environment' => false]);

        $this->assertSame(asset('images/virtual-gallery-environment.png'), $gallery->automaticEnvironmentUrl());
        $this->assertSame(
            Storage::disk('public')->url('virtual-galleries/environments/custom.webp'),
            $customGallery->automaticEnvironmentUrl(),
        );
        $this->assertNull($disabledGallery->automaticEnvironmentUrl());
    }
}
