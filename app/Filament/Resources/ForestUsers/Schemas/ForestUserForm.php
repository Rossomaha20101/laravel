<?php

namespace App\Filament\Resources\ForestUsers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ForestUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('nickname'),
                Select::make('animal_type_id')
                    ->relationship('animalType', 'name')
                    ->required(),
                Select::make('gender')
                    ->options(['M' => 'M', 'F' => 'F'])
                    ->required(),
                DatePicker::make('birth_date')
                    ->required(),
                TextInput::make('best_friend_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
            ]);
    }
}
