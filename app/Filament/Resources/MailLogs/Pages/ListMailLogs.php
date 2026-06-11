<?php

namespace App\Filament\Resources\MailLogs\Pages;

use App\Filament\Resources\MailLogs\MailLogResource;
use App\Models\MailLog;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListMailLogs extends ListRecords
{
    protected static string $resource = MailLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('prune')
                ->label(__('mail_log.actions.prune'))
                ->icon(Heroicon::OutlinedClock)
                ->color('gray')
                ->outlined()
                ->requiresConfirmation()
                ->modalIcon(Heroicon::OutlinedClock)
                ->modalDescription(__('mail_log.actions.prune_confirm'))
                ->action(function (): void {
                    $pruned = (new MailLog())->pruneAll();

                    Notification::make()
                        ->title(__('mail_log.actions.pruned', ['count' => $pruned]))
                        ->success()
                        ->send();
                }),
            Action::make('pruneAll')
                ->label(__('mail_log.actions.prune_all'))
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->requiresConfirmation()
                ->modalIcon(Heroicon::OutlinedExclamationTriangle)
                ->modalDescription(__('mail_log.actions.prune_all_confirm'))
                ->modalSubmitActionLabel(__('mail_log.actions.prune_all_submit'))
                ->action(function (): void {
                    $pruned = MailLog::query()->delete();

                    Notification::make()
                        ->title(__('mail_log.actions.pruned', ['count' => $pruned]))
                        ->success()
                        ->send();
                }),
        ];
    }
}
