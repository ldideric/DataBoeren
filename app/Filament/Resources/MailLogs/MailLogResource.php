<?php

namespace App\Filament\Resources\MailLogs;

use App\Enums\MailEvent;
use App\Filament\Resources\MailLogs\Pages\ListMailLogs;
use App\Filament\Resources\MailLogs\Pages\ViewMailLog;
use App\Filament\Resources\MailLogs\Schemas\MailLogInfolist;
use App\Filament\Resources\MailLogs\Tables\MailLogsTable;
use App\Models\MailLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MailLogResource extends Resource
{
    protected static ?string $model = MailLog::class;

    protected static string|null|BackedEnum $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?int $navigationSort = 99;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('navigation.groups.system');
    }

    public static function getModelLabel(): string
    {
        return __('navigation.mail_log.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('navigation.mail_log.plural');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) MailLog::where('event', MailEvent::Failed)
            ->where('created_at', '>=', now()->subDay())
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return MailLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MailLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailLogs::route('/'),
            'view' => ViewMailLog::route('/{record}'),
        ];
    }
}
