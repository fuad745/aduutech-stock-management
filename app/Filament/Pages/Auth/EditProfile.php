<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    protected function getProfileFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255),
            FileUpload::make('avatar')
                ->label('Avatar')
                ->image()
                ->imageEditor()
                ->directory('avatars')
                ->disk('public')
                ->avatar()
                ->maxSize(2048)
                ->visibility('public')
                ->nullable(),
        ];
    }

    protected function getPasswordFormSchema(): array
    {
        return [
            TextInput::make('current_password')
                ->label('Current password')
                ->password()
                ->revealable()
                ->rule('current_password')
                ->requiredWith('password'),
            TextInput::make('password')
                ->label('New password')
                ->password()
                ->revealable()
                ->confirmed()
                ->minLength(8)
                ->nullable(),
            TextInput::make('password_confirmation')
                ->label('Confirm new password')
                ->password()
                ->revealable()
                ->nullable(),
        ];
    }
}
