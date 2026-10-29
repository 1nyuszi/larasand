<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->autocomplete('new-password')
                    ->afterStateHydrated(fn (TextInput $component) => $component->state(null))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(12)
                    ->maxLength(255)
                    ->confirmed()
                    ->saved(fn (?string $state): bool => filled($state))
                    ->helperText('At least 12 characters. Leave blank when editing to keep the current password.'),
                TextInput::make('password_confirmation')
                    ->password()
                    ->autocomplete('new-password')
                    ->saved(false)
                    ->label('Confirm password'),
                Toggle::make('is_admin')
                    ->label('Administrator')
                    ->default(false)
                    ->helperText('Administrators can manage all blog content and users. You cannot revoke your own access.'),
            ]);
    }
}
