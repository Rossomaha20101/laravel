<?php

namespace App\Filament\Resources\AnimalTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnimalTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
            ]);
    }
}
