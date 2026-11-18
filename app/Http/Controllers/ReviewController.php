<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        Review::create([
            'product_id' => $product->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'rating' => (int) $data['rating'],
            'comment' => $data['comment'],
            'is_approved' => true,
        ]);

        return redirect(url()->previous().'#reviews')
            ->with('success', 'Terima kasih! Review Anda telah berhasil dikirim dan ditampilkan.');
    }
}
