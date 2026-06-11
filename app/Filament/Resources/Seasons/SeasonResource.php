<?php

namespace App\Filament\Resources\Seasons;

use App\Filament\Resources\Seasons\Pages\CreateSeason;
use App\Filament\Resources\Seasons\Pages\EditSeason;
use App\Filament\Resources\Seasons\Pages\ListSeasons;
use App\Filament\Resources\Seasons\Pages\ViewSeason;
use App\Filament\Resources\Seasons\RelationManagers\CampsitePricesRelationManager;
use App\Filament\Resources\Seasons\RelationManagers\PeriodsRelationManager;
use App\Filament\Resources\Seasons\Schemas\SeasonForm;
use App\Filament\Resources\Seasons\Schemas\SeasonInfolist;
use App\Filament\Resources\Seasons\Tables\SeasonsTable;
use App\Models\Season;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SeasonResource extends Resource
{
    protected static ?string $model = Season::class;

    protected static string|null|BackedEnum $navigationIcon = Heroicon::OutlinedSun;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('navigation.groups.campsite');
    }

    public static function getModelLabel(): string
    {
        return __('navigation.season.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.season.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return SeasonForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SeasonInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SeasonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PeriodsRelationManager::class,
            CampsitePricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeasons::route('/'),
            'create' => CreateSeason::route('/create'),
            'view' => ViewSeason::route('/{record}'),
            'edit' => EditSeason::route('/{record}/edit'),
        ];
    }
}
