<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;

class ReviewResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-alt-2';
    protected static ?string $navigationLabel = 'Ulasan pengguna';
    protected static ?string $navigationGroup = 'Manajemen Pengguna';
     protected static ?int $navigationSort = 2;
    public static function getLabel(): string
    {
        return 'Review';
    }
    public static function getPluralLabel(): string
    {
        return 'Kelola Review';
    }
   
    public static function getModel(): string
    {
        return Review::class;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->label('User')
                ->disabled(),

            Forms\Components\Select::make('product_id')
                ->relationship('product', 'name')
                ->label('Produk')
                ->disabled(),

            Forms\Components\TextInput::make('rating')
                ->numeric()
                ->minValue(1)
                ->maxValue(5)
                ->required(),

            Forms\Components\Textarea::make('comment')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->label('User'),
            Tables\Columns\TextColumn::make('product.name')->label('Produk'),
            Tables\Columns\TextColumn::make('rating'),
            Tables\Columns\TextColumn::make('comment')->limit(50),
            Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }

}
