<?php

namespace App\Filament\Resources;

use App\Models\Product;
use App\Filament\Resources\ProductResource\Pages;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Facades\Log;
use Filament\Resources\Resource;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;
use Filament\Tables\Columns\ImageColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

            Forms\Components\TextInput::make('slug')
                ->required()
                ->disabled(),

            Forms\Components\Textarea::make('description')
                ->rows(4),

            Forms\Components\TextInput::make('price')
                ->required()
                ->numeric()
                ->mask(fn(Forms\Components\TextInput\Mask $mask) =>
                    $mask->money('', '.', 0)
                ),

            Forms\Components\TextInput::make('stock')
                ->required()
                ->numeric(),

            Forms\Components\Select::make('status')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                ])
                ->required(),

            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->required(),

            FileUpload::make('image')
                ->image()
                ->imagePreviewHeight('150')
                ->label('Gambar Produk')
                ->disk('public')
                ->directory('')
                ->preserveFilenames()
                ->visibility('public')
                ->getUploadedFileNameForStorageUsing(function ($file) {
                    return $file->getClientOriginalName();
                })
                ->getUploadedFileUrlUsing(function ($file): ?string {
                    return $file ? asset('storage/' . $file) : null;
                }),
            Forms\Components\TextInput::make('weight')
                ->numeric()
                ->suffix('gram')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('image')
                ->label('Foto Produk')
                ->disk('public')
                ->visibility('public')
                ->url(fn ($record) => asset('storage/' . $record->image))
                ->size(60),

            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('category.name')
                ->label('Kategori')
                ->sortable(),

            Tables\Columns\TextColumn::make('price')
                ->money('IDR', true)
                ->sortable(),

            Tables\Columns\TextColumn::make('stock')
                ->sortable(),

            Tables\Columns\BadgeColumn::make('status')
                ->enum([
                    'draft' => 'Draft',
                    'published' => 'Published',
                ])
                ->colors([
                    'draft' => 'secondary',
                    'published' => 'success',
                ]),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                ]),
            Tables\Filters\SelectFilter::make('category_id')
                ->relationship('category', 'name')
                ->label('Kategori'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): string
    {
        return 'Produk';
    }

    public static function getPluralLabel(): string
    {
        return 'Produk';
    }
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Manajemen Produk';
    protected static ?int $navigationSort = 1;

}