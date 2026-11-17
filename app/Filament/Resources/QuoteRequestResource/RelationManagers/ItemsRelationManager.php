<?php

namespace App\Filament\Resources\QuoteRequestResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Produk Diminta';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Produk'),
                Tables\Columns\TextColumn::make('quantity')->label('Jumlah')->alignCenter(),
            ]);
    }
}
