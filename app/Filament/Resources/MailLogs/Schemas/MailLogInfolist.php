<?php

namespace App\Filament\Resources\MailLogs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;

class MailLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('mail_log.sections.event'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('event')
                            ->label(__('mail_log.fields.event'))
                            ->badge(),
                        TextEntry::make('created_at')
                            ->label(__('mail_log.fields.occurred_at'))
                            ->dateTime('d/m/Y H:i:s'),
                        TextEntry::make('mailable')
                            ->label(__('mail_log.fields.mailable'))
                            ->fontFamily(FontFamily::Mono)
                            ->placeholder('—'),
                        TextEntry::make('recipient')
                            ->label(__('mail_log.fields.recipient'))
                            ->copyable()
                            ->placeholder('—'),
                        TextEntry::make('subject')
                            ->label(__('mail_log.fields.subject'))
                            ->columnSpanFull()
                            ->placeholder('—'),
                    ]),

                Section::make(__('mail_log.sections.delivery'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('message_id')
                            ->label(__('mail_log.fields.message_id'))
                            ->fontFamily(FontFamily::Mono)
                            ->copyable()
                            ->placeholder('—'),
                        TextEntry::make('job_id')
                            ->label(__('mail_log.fields.job_id'))
                            ->fontFamily(FontFamily::Mono)
                            ->copyable()
                            ->placeholder('—'),
                        TextEntry::make('connection')
                            ->label(__('mail_log.fields.connection'))
                            ->placeholder('—'),
                        TextEntry::make('queue')
                            ->label(__('mail_log.fields.queue'))
                            ->placeholder('—'),
                        TextEntry::make('attempt')
                            ->label(__('mail_log.fields.attempt'))
                            ->placeholder('—'),
                    ]),

                Section::make(__('mail_log.sections.error'))
                    ->visible(fn ($record): bool => filled($record->error))
                    ->schema([
                        TextEntry::make('error')
                            ->hiddenLabel()
                            ->color('danger')
                            ->fontFamily(FontFamily::Mono)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('mail_log.sections.context'))
                    ->visible(fn ($record): bool => filled($record->context))
                    ->schema([
                        TextEntry::make('context')
                            ->hiddenLabel()
                            ->formatStateUsing(fn ($state): string => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
                            ->fontFamily(FontFamily::Mono)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
