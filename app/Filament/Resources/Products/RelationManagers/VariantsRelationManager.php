<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Sizes, Colours & Stock';

    public function form(Schema $schema): Schema
    {
        return $schema
        ->components([
            Select::make('size_id')
                ->relationship('size', 'label')
                ->getOptionLabelFromRecordUsing(
                    fn ($record) => "{$record->label} ({$record->system})"
                )
                ->required()
                ->searchable()
                ->preload()
                ->createOptionForm([
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
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->helperText('Lower numbers appear first.'),
                ])
                ->createOptionUsing(function (array $data): int {
                    return \App\Models\Size::create($data)->id;
                }),

            Select::make('color_id')
                ->relationship('color', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(50)
                        ->placeholder('Sunset Pink'),

                    ColorPicker::make('hex_code')
                        ->required(),
                ])
                ->createOptionUsing(function (array $data): int {
                    return \App\Models\Color::create($data)->id;
                }),

            TextInput::make('sku')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255)
                ->placeholder('IRV-REHUO-42-PNK'),

            TextInput::make('price_override')
                ->numeric()
                ->prefix('M')
                ->placeholder('Leave empty to use product base price'),

            TextInput::make('stock_quantity')
                ->numeric()
                ->required()
                ->minValue(0)
                ->default(0),

            Toggle::make('is_active')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('size.label')
                    ->label('Size')
                    ->formatStateUsing(
                        fn ($state, $record) => "{$state} ({$record->size->system})"
                    )
                    ->sortable(),

                ColorColumn::make('color.hex_code')
                    ->label('')
                    ->tooltip(fn ($record) => $record->color->name),

                TextColumn::make('color.name')
                    ->label('Colour')
                    ->sortable(),

                TextColumn::make('sku')
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('price_override')
                    ->label('Price')
                    ->money('LSL')
                    ->placeholder('Base price'),

                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state === 0  => 'danger',
                        $state <= 3   => 'warning',
                        default       => 'success',
                    }),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('Add Variant'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}