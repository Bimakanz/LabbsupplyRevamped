<?php

namespace App\Http\Controllers;

use App\Support\ImageProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageRemoveBackgroundController extends Controller
{
    public function __invoke(Request $request, ImageProcessor $processor): JsonResponse
    {
        abort_unless($request->user() !== null, 403);

        $data = $request->validate([
            'image' => ['required', 'string'],
            'tolerance' => ['nullable', 'integer', 'min:0', 'max:100'],
            'feather' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        if (! preg_match('/^data:image\/(png|jpeg|webp);base64,/', $data['image'])) {
            return response()->json(['message' => 'Format gambar tidak valid.'], 422);
        }

        $binary = base64_decode(substr($data['image'], strpos($data['image'], ',') + 1), true);

        if ($binary === false) {
            return response()->json(['message' => 'Gambar tidak dapat dibaca.'], 422);
        }

        $webp = $processor->removeBackgroundFromBinary(
            $binary,
            (int) ($data['tolerance'] ?? 32),
            (int) ($data['feather'] ?? 2),
        );

        return response()->json([
            'image' => 'data:image/webp;base64,'.base64_encode($webp),
        ]);
    }
}
