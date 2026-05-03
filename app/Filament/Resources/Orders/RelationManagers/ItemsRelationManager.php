<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $relatedResource = OrderResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product_name')
            ->columns([
                TextColumn::make('product_name')
                    ->label('Product'),
                TextColumn::make('product_sku')
                    ->label('SKU'),
                TextColumn::make('unit_price')
                    ->money('USD')
                    ->label('Unit Price'),
                TextColumn::make('quantity'),
                TextColumn::make('subtotal')
                    ->money('USD'),
            ])
            ->paginated(false)
            ->headerActions([]);  // no adding items from admin
    }
}
