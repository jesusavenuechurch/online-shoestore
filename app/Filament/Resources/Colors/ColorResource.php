<?php

namespace App\Filament\Resources\Colors;

use App\Filament\Resources\Colors\Pages\ManageColors;
use App\Models\Color;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ColorResource extends Resource
{
    protected static ?string $model = Color::class;

   // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

   // protected static ?string $navigationGroup = 'Catalogue Settings';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('Midnight Black'),

                ColorPicker::make('hex_code')
                    ->required()
                    ->helperText('Pick the closest visual match for the storefront swatches.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('hex_code')
                    ->label('')
                    ->copyable(false),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('hex_code')
                    ->label('Hex')
                    ->color('gray'),

                TextColumn::make('variants_count')
                    ->label('Used in variants')
                    ->counts('variants')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageColors::route('/'),
        ];
    }
}