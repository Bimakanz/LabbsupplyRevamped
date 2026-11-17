<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImageProcessor
{
    /**
     * Convert any GD-readable image to a WebP file on the public disk.
     */
    public function storeAsWebp(string $sourcePath, string $directory = 'products', int $quality = 90): string
    {
        $binary = $this->toWebp($sourcePath, $quality);
        $path = trim($directory, '/').'/'.Str::uuid().'.webp';

        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    public function toWebp(string $sourcePath, int $quality = 90): string
    {
        return $this->binaryToWebp((string) file_get_contents($sourcePath), $quality);
    }

    public function binaryToWebp(string $binary, int $quality = 90): string
    {
        $image = @imagecreatefromstring($binary);

        if (! $image) {
            throw new RuntimeException('Gambar tidak dapat dibaca.');
        }

        if (max(imagesx($image), imagesy($image)) > 1200) {
            $image = $this->resizeToFit($image, 1200);
        }

        imagepalettetotruecolor($image);
        imagesavealpha($image, true);
        imagealphablending($image, true);

        ob_start();
        imagewebp($image, null, $quality);
        $binary = (string) ob_get_clean();

        imagedestroy($image);

        if ($binary === '') {
            throw new RuntimeException('Gagal mengubah gambar ke WebP.');
        }

        return $binary;
    }

    /**
     * Remove the background using a border flood fill.
     *
     * @return string WebP binary with transparent background.
     */
    public function removeBackground(string $sourcePath, int $tolerance = 28, int $feather = 2): string
    {
        return $this->removeBackgroundFromBinary((string) file_get_contents($sourcePath), $tolerance, $feather);
    }

    public function removeBackgroundFromBinary(string $binary, int $tolerance = 28, int $feather = 2): string
    {
        $image = @imagecreatefromstring($binary);

        if (! $image) {
            throw new RuntimeException('Gambar tidak dapat dibaca.');
        }

        if (max(imagesx($image), imagesy($image)) > 1000) {
            $image = $this->resizeToFit($image, 1000);
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $tolerance = max(0, min(100, $tolerance));
        $feather = max(0, min(10, $feather));

        $background = $this->detectBackgroundColor($image, $width, $height);
        $hardLimit = $tolerance * 4.5;
        $softLimit = $hardLimit + ($feather * 20);

        imagealphablending($image, false);
        imagesavealpha($image, true);

        $visited = str_repeat("\0", $width * $height);
        $queue = new \SplQueue;

        $seed = function (int $x, int $y) use (&$queue, &$visited, $image, $background, $hardLimit, $width): void {
            $index = $y * $width + $x;

            if ($visited[$index] !== "\0") {
                return;
            }

            if ($this->distanceTo($image, $x, $y, $background) > $hardLimit) {
                return;
            }

            $visited[$index] = "\1";
            $queue->enqueue($index);
        };

        for ($x = 0; $x < $width; $x++) {
            $seed($x, 0);
            $seed($x, $height - 1);
        }

        for ($y = 0; $y < $height; $y++) {
            $seed(0, $y);
            $seed($width - 1, $y);
        }

        while (! $queue->isEmpty()) {
            $index = $queue->dequeue();
            $x = $index % $width;
            $y = intdiv($index, $width);

            $this->setAlpha($image, $x, $y, 127);

            foreach ([[$x - 1, $y], [$x + 1, $y], [$x, $y - 1], [$x, $y + 1]] as [$nx, $ny]) {
                if ($nx < 0 || $ny < 0 || $nx >= $width || $ny >= $height) {
                    continue;
                }

                $neighbour = $ny * $width + $nx;

                if ($visited[$neighbour] !== "\0") {
                    continue;
                }

                $distance = $this->distanceTo($image, $nx, $ny, $background);

                if ($distance <= $hardLimit) {
                    $visited[$neighbour] = "\1";
                    $queue->enqueue($neighbour);
                } elseif ($feather > 0 && $distance <= $softLimit) {
                    $visited[$neighbour] = "\2";
                    $this->setAlpha($image, $nx, $ny, 96);
                }
            }
        }

        imagealphablending($image, true);

        ob_start();
        imagewebp($image, null, 90);
        $binary = (string) ob_get_clean();

        imagedestroy($image);

        return $binary;
    }

    private function load(string $path): \GdImage
    {
        $image = @imagecreatefromstring((string) file_get_contents($path));

        if (! $image) {
            throw new RuntimeException('Gambar tidak dapat dibaca.');
        }

        return $image;
    }

    private function resizeToFit(\GdImage $image, int $max): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $ratio = $max / max($width, $height);

        $resized = imagecreatetruecolor((int) round($width * $ratio), (int) round($height * $ratio));
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagefill($resized, 0, 0, imagecolorallocatealpha($resized, 0, 0, 0, 127));
        imagecopyresampled($resized, $image, 0, 0, 0, 0, imagesx($resized), imagesy($resized), $width, $height);
        imagedestroy($image);

        return $resized;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function detectBackgroundColor(\GdImage $image, int $width, int $height): array
    {
        $reds = $greens = $blues = [];
        $stepX = max(1, intdiv($width, 50));
        $stepY = max(1, intdiv($height, 50));

        for ($x = 0; $x < $width; $x += $stepX) {
            foreach ([0, $height - 1] as $y) {
                $color = imagecolorat($image, $x, $y);
                $reds[] = ($color >> 16) & 0xFF;
                $greens[] = ($color >> 8) & 0xFF;
                $blues[] = $color & 0xFF;
            }
        }

        for ($y = 0; $y < $height; $y += $stepY) {
            foreach ([0, $width - 1] as $x) {
                $color = imagecolorat($image, $x, $y);
                $reds[] = ($color >> 16) & 0xFF;
                $greens[] = ($color >> 8) & 0xFF;
                $blues[] = $color & 0xFF;
            }
        }

        sort($reds);
        sort($greens);
        sort($blues);

        $middle = intdiv(count($reds), 2);

        return [$reds[$middle], $greens[$middle], $blues[$middle]];
    }

    /**
     * @param  array{0: int, 1: int, 2: int}  $background
     */
    private function distanceTo(\GdImage $image, int $x, int $y, array $background): float
    {
        $color = imagecolorat($image, $x, $y);
        [$r, $g, $b] = [$color >> 16 & 0xFF, $color >> 8 & 0xFF, $color & 0xFF];

        return sqrt(
            ($r - $background[0]) ** 2
            + ($g - $background[1]) ** 2
            + ($b - $background[2]) ** 2
        );
    }

    private function setAlpha(\GdImage $image, int $x, int $y, int $alpha): void
    {
        $color = imagecolorat($image, $x, $y);
        $rgb = $color & 0xFFFFFF;

        imagesetpixel($image, $x, $y, ($alpha << 24) | $rgb);
    }
}
