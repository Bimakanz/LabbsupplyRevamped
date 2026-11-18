<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Penjualan';

    protected static ?string $recordTitleAttribute = 'number';

    public static function getNavigationBadge(): ?string
    {
        $count = Order::query()->where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Status Pesanan')
                    ->schema([
                        Forms\Components\TextInput::make('number')->label('Nomor pesanan')->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Menunggu Pembayaran',
                                'processing' => 'Diproses',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required(),
                        Forms\Components\Select::make('payment_status')
                            ->label('Status pembayaran')
                            ->options([
                                'unpaid' => 'Belum Dibayar',
                                'paid' => 'Lunas',
                                'failed' => 'Gagal',
                                'expired' => 'Kedaluwarsa',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('notes')->label('Catatan')->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Data Pelanggan')
                    ->schema([
                        Forms\Components\TextInput::make('billing_first_name')->label('Nama depan')->disabled(),
                        Forms\Components\TextInput::make('billing_last_name')->label('Nama belakang')->disabled(),
                        Forms\Components\TextInput::make('billing_email')->label('Email')->disabled(),
                        Forms\Components\TextInput::make('billing_phone')->label('Telepon')->disabled(),
                        Forms\Components\TextInput::make('billing_company')->label('Perusahaan')->disabled(),
                        Forms\Components\TextInput::make('billing_address_1')->label('Alamat')->disabled()->columnSpanFull(),
                        Forms\Components\TextInput::make('billing_city')->label('Kota')->disabled(),
                        Forms\Components\TextInput::make('billing_postcode')->label('Kode pos')->disabled(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label('Nomor')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('billing_first_name')
                    ->label('Pelanggan')
                    ->formatStateUsing(fn (Order $record): string => trim($record->billing_first_name.' '.$record->billing_last_name))
                    ->searchable(['billing_first_name', 'billing_last_name']),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Item')
                    ->counts('items')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'processing' => 'Diproses',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'paid' ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-m-pencil-square')
                    ->iconButton()
                    ->tooltip('Edit'),
                Tables\Actions\Action::make('markPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-m-check-circle')
                    ->iconButton()
                    ->tooltip('Tandai lunas')
                    ->color('success')
                    ->visible(fn (Order $record): bool => ! $record->isPaid())
                    ->requiresConfirmation()
                    ->action(function (Order $record): void {
                        $record->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                            'paid_at' => now(),
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('markPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn ($records) => $records->each->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                        'paid_at' => now(),
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
