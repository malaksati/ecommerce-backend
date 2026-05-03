<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Status')->schema([
                    Select::make('status')
                        ->options([
                            'pending'    => 'Pending',
                            'confirmed'  => 'Confirmed',
                            'processing' => 'Processing',
                            'shipped'    => 'Shipped',
                            'delivered'  => 'Delivered',
                            'cancelled'  => 'Cancelled',
                            'refunded'   => 'Refunded',
                        ])
                        ->required(),

                    Select::make('payment_status')
                        ->options([
                            'unpaid'   => 'Unpaid',
                            'paid'     => 'Paid',
                            'refunded' => 'Refunded',
                        ])
                        ->required(),
                ])->columns(2),

                Section::make('Shipping Address')->schema([
                    TextInput::make('shipping_full_name')->disabled(),
                    TextInput::make('shipping_phone')->disabled(),
                    TextInput::make('shipping_street')->disabled(),
                    TextInput::make('shipping_city')->disabled(),
                    TextInput::make('shipping_country')->disabled(),
                    TextInput::make('shipping_postal_code')->disabled(),
                ])->columns(2),

                Section::make('Order Totals')->schema([
                    TextInput::make('subtotal')->disabled()->prefix('$'),
                    TextInput::make('shipping_cost')->disabled()->prefix('$'),
                    TextInput::make('discount')->disabled()->prefix('$'),
                    TextInput::make('total')->disabled()->prefix('$'),
                ])->columns(2),

                Textarea::make('notes')
                    ->columnSpanFull()
                    ->disabled(),
            ]);
    }
}
