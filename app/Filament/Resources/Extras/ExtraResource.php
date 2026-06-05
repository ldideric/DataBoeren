<?php

namespace App\Filament\Resources\Extras;

use App\Filament\Resources\Extras\Pages\CreateExtra;
use App\Filament\Resources\Extras\Pages\EditExtra;
use App\Filament\Resources\Extras\Pages\ListExtras;
use App\Filament\Resources\Extras\Pages\ViewExtra;
use App\Filament\Resources\Extras\Schemas\ExtraForm;
use App\Filament\Resources\Extras\Schemas\ExtraInfolist;
use App\Filament\Resources\Extras\Tables\ExtrasTable;
use App\Models\Extra;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExtraResource extends Resource
{
    protected static ?string $model = Extra::class;

    protected static string|null|BackedEnum $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('navigation.groups.campsite');
    }

    public static function getModelLabel(): string
    {
        return __('navigation.extra.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.extra.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return ExtraForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExtraInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExtrasTable::configure($table);
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
            'index' => ListExtras::route('/'),
            'create' => CreateExtra::route('/create'),
            'view' => ViewExtra::route('/{record}'),
            'edit' => EditExtra::route('/{record}/edit'),
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
