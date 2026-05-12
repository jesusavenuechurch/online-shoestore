<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    //protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    //protected static ?string $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set, $record) {
                                if (! $record) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->columnSpan(1)
                            ->helperText('Auto-generated. Used in the storefront URL.'),

                        TextInput::make('brand')
                            ->maxLength(255)
                            ->placeholder('Nike')
                            ->columnSpan(1),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        TextInput::make('base_price')
                            ->required()
                            ->numeric()
                            ->prefix('M')
                            ->minValue(0)
                            ->columnSpan(1)
                            ->helperText('Variants can override this price per size/color.'),

                        Toggle::make('is_active')
                            ->default(true)
                            ->columnSpan(1)
                            ->helperText('Inactive products are hidden from the storefront.'),
                    ]),

                Section::make('Description')
                    ->collapsed()
                    ->schema([
                        RichEditor::make('description')
                            ->columnSpanFull(),
                    ]),

                    Section::make('Images')
                        ->collapsed()
                        ->schema([
                            FileUpload::make('images')
                                ->disk('public')                    // explicit public disk
                                ->directory('products')             // saves to storage/app/public/products/
                                ->multiple()
                                ->image()
                                ->imageEditor()
                                ->reorderable()
                                ->maxFiles(13)
                                ->panelLayout('grid')
                                ->columnSpanFull()
                                ->preserveFilenames(),
                        ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('brand')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('base_price')
                    ->money('LSL')
                    ->sortable(),

                TextColumn::make('variants_count')
                    ->label('Variants')
                    ->counts('variants')
                    ->badge()
                    ->color('info'),

                // Total stock across all variants
                TextColumn::make('total_stock')
                    ->label('Total Stock')
                    ->getStateUsing(
                        fn (Product $record) => $record->variants()->sum('stock_quantity')
                    )
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state === 0  => 'danger',
                        $state <= 5   => 'warning',
                        default       => 'success',
                    }),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit'   => EditProduct::route('/{record}/edit'),
        ];
    }
}