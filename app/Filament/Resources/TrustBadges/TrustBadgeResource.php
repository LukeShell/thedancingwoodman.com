<?php

namespace App\Filament\Resources\TrustBadges;

use App\Filament\Resources\TrustBadges\Pages\CreateTrustBadge;
use App\Filament\Resources\TrustBadges\Pages\EditTrustBadge;
use App\Filament\Resources\TrustBadges\Pages\ListTrustBadges;
use App\Filament\Resources\TrustBadges\Schemas\TrustBadgeForm;
use App\Filament\Resources\TrustBadges\Tables\TrustBadgesTable;
use App\Models\TrustBadge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
class TrustBadgeResource extends Resource
{
    protected static ?string $model = TrustBadge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Configuration';
    public static function form(Schema $schema): Schema
    {
        return TrustBadgeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrustBadgesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrustBadges::route('/'),
            'create' => CreateTrustBadge::route('/create'),
            'edit' => EditTrustBadge::route('/{record}/edit'),
        ];
    }
}
