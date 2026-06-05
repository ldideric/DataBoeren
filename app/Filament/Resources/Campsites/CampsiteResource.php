<?php

namespace App\Filament\Resources\Campsites;

use App\Filament\Resources\Campsites\Pages\CreateCampsite;
use App\Filament\Resources\Campsites\Pages\EditCampsite;
use App\Filament\Resources\Campsites\Pages\ListCampsites;
use App\Filament\Resources\Campsites\Pages\ViewCampsite;
use App\Filament\Resources\Campsites\RelationManagers\PricesRelationManager;
use App\Filament\Resources\Campsites\RelationManagers\ReservationsRelationManager;
use App\Filament\Resources\Campsites\Schemas\CampsiteForm;
use App\Filament\Resources\Campsites\Schemas\CampsiteInfolist;
use App\Filament\Resources\Campsites\Tables\CampsitesTable;
use App\Models\Campsite;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CampsiteResource extends Resource
{
    protected static ?string $model = Campsite::class;

    protected static string|null|BackedEnum $navigationIcon = Heroicon::OutlinedMapPin;

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('navigation.groups.campsite');
    }

    public static function getModelLabel(): string
    {
        return __('navigation.campsite.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.campsite.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return CampsiteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CampsiteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampsitesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PricesRelationManager::class,
            ReservationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampsites::route('/'),
            'create' => CreateCampsite::route('/create'),
            'view' => ViewCampsite::route('/{record}'),
            'edit' => EditCampsite::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
