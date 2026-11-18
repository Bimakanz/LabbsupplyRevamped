<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Support\ImageProcessor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama produk')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug((string) $state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255),
                        Forms\Components\Select::make('tags')
                            ->label('Tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Harga & Stok')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->required()
                            ->prefix('Rp')
                            ->inputMode('numeric')
                            ->formatStateUsing(fn ($state): ?string => filled($state) ? number_format((int) $state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn ($state): int => (int) preg_replace('/\D/', '', (string) $state))
                            ->extraInputAttributes(['data-labs-money' => '1', 'autocomplete' => 'off'])
                            ->default(0),
                        Forms\Components\TextInput::make('stock')
                            ->label('Stok')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('stock_status')
                            ->label('Status stok')
                            ->options([
                                'in_stock' => 'Tersedia',
                                'out_of_stock' => 'Habis',
                                'on_backorder' => 'Pre-order',
                            ])
                            ->required()
                            ->default('in_stock'),
                        Forms\Components\TextInput::make('weight')
                            ->label('Berat')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('dimensions')
                            ->label('Dimensi')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Deskripsi')
                    ->schema([
                        Forms\Components\Textarea::make('short_description')
                            ->label('Deskripsi singkat')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi lengkap')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Gambar Produk')
                    ->description('Crop dikunci ke rasio 1:1. Gambar otomatis dikonversi ke WebP.')
                    ->schema([
                        Forms\Components\Repeater::make('images')
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->defaultItems(0)
                            ->addActionLabel('Tambah gambar')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => filled($state['alt'] ?? null) ? $state['alt'] : null)
                            ->schema([
                                Forms\Components\FileUpload::make('path')
                                    ->label('Gambar')
                                    ->image()
                                    ->required()
                                    ->disk('public')
                                    ->directory('products')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->imageEditorAspectRatios(['1:1'])
                                    ->imagePreviewHeight('260')
                                    ->columnSpanFull()
                                    ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => app(ImageProcessor::class)
                                        ->storeAsWebp($file->getRealPath() ?: $file->getPathname())),
                                Forms\Components\TextInput::make('alt')
                                    ->label('Alt text')
                                    ->maxLength(255),
                            ])
                            ->columns(1),
                    ]),

                Forms\Components\Section::make('Lainnya')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Unggulan'),
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images.path')
                    ->label('Gambar')
                    ->getStateUsing(fn (Product $record): ?string => $record->primary_image?->url)
                    ->circular()
                    ->width(44)
                    ->height(44),
                Tables\Columns\TextColumn::make('name')
                    ->label('Produk')
                    ->searchable()
                    ->sortable()
                    ->limit(46),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(fn (int $state): string => $state > 5 ? 'success' : ($state > 0 ? 'warning' : 'danger'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('views')
                    ->label('Dilihat')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['category', 'images']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
