<?php

namespace Modules\Order\Filament\Resources;

use App\Enums\OrderStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Order\Filament\Resources\OrderResource\Pages\EditOrder;
use Modules\Order\Filament\Resources\OrderResource\Pages\ListOrders;
use Modules\Order\Filament\Resources\OrderResource\Pages\ViewOrder;
use Modules\Order\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;
use Modules\Order\Models\Order;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationGroup = 'Orders';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('customer_name')->required()->maxLength(255),
            TextInput::make('customer_email')->email()->required()->maxLength(255),
            TextInput::make('total_amount')->numeric()->prefix('$')->disabled(),
            Select::make('status')
                ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()]))
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order #')->sortable(),
                TextColumn::make('customer_name')->searchable()->sortable(),
                TextColumn::make('customer_email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_amount')->money('USD')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (OrderStatus $state) => $state->color())
                    ->formatStateUsing(fn (OrderStatus $state) => $state->label()),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(OrderStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn (Order $record) => $record->status->canTransitionTo(OrderStatus::Confirmed))
                    ->action(fn (Order $record) => $record->transitionTo(OrderStatus::Confirmed)),
                Action::make('ship')
                    ->label('Ship')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn (Order $record) => $record->status->canTransitionTo(OrderStatus::Shipped))
                    ->action(fn (Order $record) => $record->transitionTo(OrderStatus::Shipped)),
                Action::make('deliver')
                    ->label('Deliver')
                    ->icon('heroicon-o-home')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status->canTransitionTo(OrderStatus::Delivered))
                    ->action(fn (Order $record) => $record->transitionTo(OrderStatus::Delivered)),
            ]);
    }

    public static function getRelations(): array
    {
        return [OrderItemsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
