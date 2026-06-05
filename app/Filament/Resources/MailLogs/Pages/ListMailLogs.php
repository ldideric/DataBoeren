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
                ->icon(Heroicon::OutlinedTrash)
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription(__('mail_log.actions.prune_confirm'))
                ->action(function (): void {
                    $pruned = (new MailLog())->pruneAll();

                    Notification::make()
                        ->title(__('mail_log.actions.pruned', ['count' => $pruned]))
                        ->success()
                        ->send();
                }),
        ];
    }
}
