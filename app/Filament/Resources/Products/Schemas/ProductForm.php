<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Intervention\Image\File;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Info')->schema([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(
                            fn($state, $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Select::make('category_id')
                        ->label('Category')
                        ->options(Category::pluck('name', 'id'))
                        ->searchable()
                        ->required(),

                    TextInput::make('sku')
                        ->label('SKU')
                        ->unique(ignoreRecord: true),
                    Repeater::make('images')
                        ->relationship() // 🔥 THIS IS THE KEY
                        ->label('Product Images')
                        ->schema([
                            FileUpload::make('image_url')
                                ->image()
                                ->disk('public')           
                                ->visibility('public')     
                                ->directory('products')
                                ->required(),

                            Toggle::make('is_primary')
                                ->label('Primary Image'),

                            TextInput::make('sort_order')
                                ->numeric()
                                ->default(0),
                        ])
                        ->reorderable()
                        ->defaultItems(1)
                ])->columns(2),

                Section::make('Pricing & Stock')->schema([
                    TextInput::make('price')
                        ->numeric()
                        ->prefix('$')
                        ->required(),

                    TextInput::make('sale_price')
                        ->numeric()
                        ->prefix('$')
                        ->nullable(),

                    TextInput::make('stock')
                        ->numeric()
                        ->required()
                        ->default(0),

                    Toggle::make('is_active')
                        ->default(true),

                    Toggle::make('is_featured')
                        ->default(false),
                ])->columns(2),

                Section::make('Description')->schema([
                    RichEditor::make('description')
                        ->columnSpanFull(),
                ]),
            ]);
    }
}
