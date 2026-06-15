<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

use \App\Models\MailLog;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label(__('common.first_name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label(__('common.last_name'))
                    ->required()
                    ->maxLength(255),
                $this->getEmailFormComponent(),
                Select::make('locale')
                    ->label(__('common.locale'))
                    ->options([
                        'nl' => 'Nederlands',
                        'en' => 'English',
                    ])
                    ->native(false)
                    ->default('nl'),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                Toggle::make('show_mail_logs')
                    ->label(__('navigation.mail_log.toggle'))
                    ->helperText(__('navigation.mail_log.toggle_hint'))
                    ->visible(auth()->user()->can('viewAny', MailLog::class))
                    ->inline(false)
                    ->onIcon(Heroicon::Envelope),
            ]);
    }
}
