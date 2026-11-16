<?php

namespace App\Http\Controllers;

use App\Services\CompareService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompareController extends Controller
{
    public function index(CompareService $compare): View
    {
        return view('compare.index', [
            'products' => $compare->products(),
            'ids' => $compare->ids(),
        ]);
    }

    public function toggle(Request $request, CompareService $compare): JsonResponse|RedirectResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer', 'exists:products,id']]);
        $result = $compare->toggle((int) $data['product_id']);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'added' => $result['added'],
                'count' => $compare->count(),
                'message' => $result['message'],
            ]);
        }

        return back()->with('success', $result['message']);
    }

    public function remove(Request $request, CompareService $compare): JsonResponse|RedirectResponse
    {
        $data = $request->validate(['product_id' => ['required', 'integer']]);
        $compare->remove((int) $data['product_id']);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'count' => $compare->count()]);
        }

        return back()->with('success', 'Produk dihapus dari perbandingan.');
    }

    public function clear(Request $request, CompareService $compare): JsonResponse|RedirectResponse
    {
        $compare->clear();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'count' => 0]);
        }

        return back()->with('success', 'Perbandingan dikosongkan.');
    }

    public function bar(CompareService $compare): View
    {
        return view('partials.compare-bar', ['products' => $compare->products()]);
    }
}
