<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\ProductVariant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Variants';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Variant')
                ->columns(2)
                ->schema([
                    TextInput::make('sku')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('£')
                        ->minValue(0),

                    TextInput::make('stock_quantity')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(0),

                    Toggle::make('is_active')
                        ->default(true)
                        ->inline(false),
                ]),

            Section::make('Attribute combination')
                ->description('Pick one value per attribute defined on this product.')
                ->columns(2)
                ->schema(fn () => $this->attributeFields()),
        ]);
    }

    /**
     * @return array<int, Select>
     */
    protected function attributeFields(): array
    {
        return $this->getOwnerRecord()
            ->attributes()
            ->with('values')
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($attribute) => Select::make(self::attributeKey($attribute->id))
                ->label($attribute->name)
                ->options($attribute->values->pluck('value', 'id'))
                ->required(),
            )
            ->all();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                TextColumn::make('sku')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('attributeValues.value')
                    ->label('Combination')
                    ->badge()
                    ->separator(','),

                TextColumn::make('price')
                    ->money('GBP')
                    ->sortable(),

                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->using(fn (array $data, VariantsRelationManager $livewire): Model => self::createVariant($livewire, $data)),
            ])
            ->recordActions([
                EditAction::make()
                    ->fillForm(fn (ProductVariant $record): array => self::fillVariantForm($record))
                    ->using(fn (ProductVariant $record, array $data): Model => self::updateVariant($record, $data)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function attributeKey(int $attributeId): string
    {
        return "attr_{$attributeId}";
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{0: array<string, mixed>, 1: array<int, int>}
     */
    protected static function splitAttributeData(array $data): array
    {
        $attributeValueIds = [];

        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'attr_')) {
                if ($value !== null) {
                    $attributeValueIds[] = (int) $value;
                }
                unset($data[$key]);
            }
        }

        return [$data, $attributeValueIds];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected static function createVariant(VariantsRelationManager $livewire, array $data): ProductVariant
    {
        [$attributes, $attributeValueIds] = self::splitAttributeData($data);

        /** @var ProductVariant $variant */
        $variant = $livewire->getOwnerRecord()->variants()->create($attributes);
        $variant->attributeValues()->sync($attributeValueIds);

        return $variant;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected static function updateVariant(ProductVariant $record, array $data): ProductVariant
    {
        [$attributes, $attributeValueIds] = self::splitAttributeData($data);

        $record->update($attributes);
        $record->attributeValues()->sync($attributeValueIds);

        return $record;
    }

    /**
     * @return array<string, mixed>
     */
    protected static function fillVariantForm(ProductVariant $record): array
    {
        $data = $record->only(['sku', 'price', 'stock_quantity', 'is_active']);

        foreach ($record->attributeValues as $value) {
            $data[self::attributeKey($value->product_attribute_id)] = $value->id;
        }

        return $data;
    }
}
