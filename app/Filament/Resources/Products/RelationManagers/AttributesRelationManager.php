<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'attributes';

    protected static ?string $title = 'Attributes & values';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Attribute name')
                    ->helperText('e.g. Diameter, Length, Finish')
                    ->required()
                    ->maxLength(255),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Repeater::make('values')
                    ->label('Values')
                    ->relationship()
                    ->schema([
                        TextInput::make('value')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->orderColumn('sort_order')
                    ->reorderable()
                    ->defaultItems(0)
                    ->addActionLabel('Add value')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('values_count')
                    ->label('Values')
                    ->counts('values')
                    ->badge(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make(),
            ])
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
}
