<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AddonsRelationManager extends RelationManager
{
    protected static string $relationship = 'addons';

    protected static ?string $title = 'Add-ons';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Add-on')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('£')
                        ->minValue(0),

                    TextInput::make('description')
                        ->maxLength(255)
                        ->columnSpanFull(),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_active')
                        ->default(true)
                        ->inline(false),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('price')
                    ->money('GBP')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_active'),
            ])
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
