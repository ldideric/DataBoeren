<?php

namespace App\Filament\Resources\MailLogs\Tables;

use App\Filament\Resources\MailLogs\Filters\EventFilter;
use App\Filament\Resources\MailLogs\Filters\FailuresFilter;
use App\Filament\Resources\MailLogs\Filters\MailableFilter;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MailLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('15s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('mail_log.fields.occurred_at'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('event')
                    ->label(__('mail_log.fields.event'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('mailable')
                    ->label(__('mail_log.fields.mailable'))
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('recipient')
                    ->label(__('mail_log.fields.recipient'))
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),
                TextColumn::make('subject')
                    ->label(__('mail_log.fields.subject'))
                    ->limit(40)
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState())
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('message_id')
                    ->label(__('mail_log.fields.message_id'))
                    ->fontFamily(FontFamily::Mono)
                    ->copyable()
                    ->limit(28)
                    ->placeholder('—'),
                TextColumn::make('error')
                    ->label(__('mail_log.fields.error'))
                    ->color('danger')
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState())
                    ->placeholder('—')
                    ->toggledHiddenByDefault(),
                TextColumn::make('job_id')
                    ->label(__('mail_log.fields.job_id'))
                    ->fontFamily(FontFamily::Mono)
                    ->placeholder('—')
                    ->toggledHiddenByDefault(),
                TextColumn::make('attempt')
                    ->label(__('mail_log.fields.attempt'))
                    ->placeholder('—')
                    ->toggledHiddenByDefault(),
                TextColumn::make('queue')
                    ->label(__('mail_log.fields.queue'))
                    ->placeholder('—')
                    ->toggledHiddenByDefault(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                EventFilter::make(),
                MailableFilter::make(),
                FailuresFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
            ])
            ->toolbarActions([]);
    }
}
