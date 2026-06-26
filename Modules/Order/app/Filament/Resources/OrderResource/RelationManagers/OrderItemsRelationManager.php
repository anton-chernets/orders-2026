<?php

namespace Modules\Order\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')->label('Product'),
                TextColumn::make('product_price')->money('USD')->label('Unit Price'),
                TextColumn::make('quantity'),
                TextColumn::make('subtotal')->money('USD'),
            ]);
    }
}
