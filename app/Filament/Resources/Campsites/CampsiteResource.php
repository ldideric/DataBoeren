<?php

namespace App\Filament\Resources\Campsites;

use App\Filament\Resources\Campsites\Pages\CreateCampsite;
use App\Filament\Resources\Campsites\Pages\EditCampsite;
use App\Filament\Resources\Campsites\Pages\ListCampsites;
use App\Filament\Resources\Campsites\Pages\ViewCampsite;
use App\Filament\Resources\Campsites\Schemas\CampsiteForm;
use App\Filament\Resources\Campsites\Schemas\CampsiteInfolist;
use App\Filament\Resources\Campsites\Tables\CampsitesTable;
use App\Models\Campsite;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CampsiteResource extends Resource
{
    protected static ?string $model = Campsite::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

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
            //
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
}
