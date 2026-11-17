<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function index(): View
    {
        return view('order.track', ['order' => null]);
    }

    public function track(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        $query = Order::query()->with('items')->where('number', strtoupper(trim($data['number'])));

        if (! empty($data['email'])) {
            $query->where('billing_email', $data['email']);
        }

        $order = $query->first();

        if (! $order) {
            return back()->withInput()->with('error', 'Pesanan tidak ditemukan. Periksa kembali nomor pesanan Anda.');
        }

        return view('order.track', compact('order'));
    }
}
