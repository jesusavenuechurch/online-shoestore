<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Product Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('slug', Str::slug($state))
                        ),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('brand')
                        ->maxLength(255),

                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload(),

                    TextInput::make('base_price')
                        ->required()
                        ->numeric()
                        ->prefix('M')
                        ->minValue(0),

                    Toggle::make('is_active')
                        ->default(true)
                        ->columnSpanFull(),
                ]),

            Section::make('Description')
                ->schema([
                    RichEditor::make('description')
                        ->columnSpanFull(),
                ]),

            Section::make('Images')
                ->schema([
                    FileUpload::make('images')
                        ->multiple()
                        ->image()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}