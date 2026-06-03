<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
                                'slug',
                                Str::slug($state ?? ''),
                            )),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('parent_id')
                            ->label('Parent category')
                            ->relationship(
                                'parent',
                                'name',
                                fn ($query, ?Category $record) => $record
                                    ? $query->whereKeyNot($record->id)
                                    : $query,
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Top level'),

                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),

                        Textarea::make('description')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }
}
