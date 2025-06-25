<?php
namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Order Berjalan';
    protected static ?string $pluralLabel = 'Pesanan Berlangsung';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama')->disabled(),
            Forms\Components\TextInput::make('phone')->label('Telepon')->disabled(),
            Forms\Components\Textarea::make('address')->label('Alamat')->disabled(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])
                ->required(),

            Forms\Components\Select::make('payment_method')->label('Metode Pembayaran')->disabled(),
            Forms\Components\Select::make('shipping_method')->label('Kurir')->disabled(),

            Forms\Components\Repeater::make('orderItems')
                ->label('Daftar Produk')
                ->relationship()
                ->schema([
                    Forms\Components\TextInput::make('product.name')->label('Produk')->disabled(),
                    Forms\Components\TextInput::make('quantity')->label('Jumlah')->disabled(),
                    Forms\Components\TextInput::make('price')->label('Harga')->disabled(),
                ])
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Order ID')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('User'),
                Tables\Columns\TextColumn::make('total')->money('IDR')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'pending',
                        'info' => 'paid',
                        'warning' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')->label('Pembayaran'),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'processing' => 'Processing',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah Status'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // 👇 Di sinilah kita filter agar hanya pesanan yang belum selesai yang tampil
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNot('status', 'completed');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
