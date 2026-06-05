<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
            ]);
    }
}
