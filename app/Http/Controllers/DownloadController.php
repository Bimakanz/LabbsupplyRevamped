<?php

namespace App\Http\Controllers;

use App\Models\Download;
use Illuminate\View\View;

class DownloadController extends Controller
{
    public function index(): View
    {
        $downloads = Download::query()->where('is_active', true)->orderBy('sort_order')->get();

        if ($downloads->isEmpty()) {
            $downloads = collect([
                (object) [
                    'title' => 'Katalog Produk Lab Supply 2026',
                    'description' => 'Daftar lengkap produk laboratorium beserta spesifikasi dan harga.',
                    'file_url' => '#',
                ],
                (object) [
                    'title' => 'Sertifikat ISO 9001:2015',
                    'description' => 'Sertifikat sistem manajemen mutu Lab Supply.',
                    'file_url' => '#',
                ],
                (object) [
                    'title' => 'Company Profile',
                    'description' => 'Profil perusahaan, visi misi, dan layanan Lab Supply.',
                    'file_url' => '#',
                ],
            ]);
        }

        return view('download.index', compact('downloads'));
    }
}
