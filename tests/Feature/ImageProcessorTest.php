<?php

namespace Tests\Feature;

use App\Support\ImageProcessor;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageProcessorTest extends TestCase
{
    public function test_background_removal_makes_the_border_transparent(): void
    {
        $source = $this->makeTestImage();

        $binary = (new ImageProcessor)->removeBackground($source, 40, 2);
        $processed = imagecreatefromstring($binary);

        $corner = imagecolorat($processed, 1, 1);
        $center = imagecolorat($processed, 25, 25);

        $this->assertSame(127, ($corner >> 24) & 0x7F);
        $this->assertLessThan(127, ($center >> 24) & 0x7F);
    }

    public function test_images_are_stored_as_webp_on_the_public_disk(): void
    {
        Storage::fake('public');

        $source = $this->makeTestImage();

        $path = (new ImageProcessor)->storeAsWebp($source);

        Storage::disk('public')->assertExists($path);
        $this->assertStringEndsWith('.webp', $path);
        $this->assertSame('image/webp', getimagesizefromstring(Storage::disk('public')->get($path))['mime']);
    }

    private function makeTestImage(): string
    {
        $image = imagecreatetruecolor(50, 50);
        imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
        imagefilledrectangle($image, 15, 15, 35, 35, imagecolorallocate($image, 200, 30, 30));

        $path = tempnam(sys_get_temp_dir(), 'labsupply').'.png';
        imagepng($image, $path);
        imagedestroy($image);

        return $path;
    }
}
