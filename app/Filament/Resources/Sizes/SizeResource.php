<?php

namespace App\Filament\Resources\Sizes;

use App\Filament\Resources\Sizes\Pages\ManageSizes;
use App\Models\Size;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SizeResource extends Resource
{
    protected static ?string $model = Size::class;

   // protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    //protected static ?string $navigationGroup = 'Catalogue Settings';

    //protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->maxLength(10)
                    ->placeholder('42'),

                Select::make('system')
                    ->required()
                    ->options([
                        'EU' => 'EU (European)',
                        'US' => 'US (United States)',
                        'UK' => 'UK (United Kingdom)',
                    ])
                    ->default('EU'),

                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first in size selectors.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('system')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),

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
            'index' => ManageSizes::route('/'),
        ];
    }
}