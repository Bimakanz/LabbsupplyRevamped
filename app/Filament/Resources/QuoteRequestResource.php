<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteRequestResource\Pages;
use App\Filament\Resources\QuoteRequestResource\RelationManagers\ItemsRelationManager;
use App\Models\QuoteRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Penjualan';

    protected static ?string $navigationLabel = 'Permintaan Quote';

    public static function getNavigationBadge(): ?string
    {
        $count = QuoteRequest::query()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('Nama')->disabled(),
                Forms\Components\TextInput::make('company')->label('Perusahaan')->disabled(),
                Forms\Components\TextInput::make('email')->label('Email')->disabled(),
                Forms\Components\TextInput::make('phone')->label('Telepon')->disabled(),
                Forms\Components\Textarea::make('message')->label('Kebutuhan')->disabled()->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'new' => 'Baru',
                        'contacted' => 'Dihubungi',
                        'quoted' => 'Sudah Diquote',
                        'closed' => 'Selesai',
                    ])
                    ->required(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('company')->label('Perusahaan')->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('phone')->label('Telepon'),
                Tables\Columns\TextColumn::make('items_count')->label('Produk')->counts('items')->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Baru',
                        'contacted' => 'Dihubungi',
                        'quoted' => 'Diquote',
                        'closed' => 'Selesai',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'contacted' => 'info',
                        'quoted' => 'primary',
                        'closed' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'new' => 'Baru',
                    'contacted' => 'Dihubungi',
                    'quoted' => 'Diquote',
                    'closed' => 'Selesai',
                ]),
            ], layout: Tables\Enums\FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-m-pencil-square')
                    ->iconButton()
                    ->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-m-trash')
                    ->iconButton()
                    ->tooltip('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuoteRequests::route('/'),
            'edit' => Pages\EditQuoteRequest::route('/{record}/edit'),
        ];
    }
}
