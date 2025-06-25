<?php

namespace App\Filament\Resources\ProdukResource\Widgets;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget; 


class StatsOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Produk', \App\Models\Product::count()),
            // Card::make('Total User', \App\Models\User::count()),
        ];
    }
}

