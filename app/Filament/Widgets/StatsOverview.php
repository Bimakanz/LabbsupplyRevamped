<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\QuoteRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $revenue = (float) Order::query()->where('payment_status', 'paid')->sum('total');
        $monthRevenue = (float) Order::query()
            ->where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        return [
            Stat::make('Pendapatan Bulan Ini', rupiah($monthRevenue))
                ->description('Total: '.rupiah($revenue))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Pesanan Masuk', (string) Order::query()->count())
                ->description(Order::query()->where('status', 'pending')->count().' menunggu pembayaran')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning'),
            Stat::make('Produk Aktif', (string) Product::query()->where('is_active', true)->count())
                ->description(Product::query()->where('stock', '<', 5)->count().' produk stok menipis')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('info'),
            Stat::make('Permintaan Quote', (string) QuoteRequest::query()->where('status', 'new')->count())
                ->description(ContactMessage::query()->where('is_read', false)->count().' pesan belum dibaca')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),
        ];
    }
}
