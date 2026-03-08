<?php

namespace App\Filament\Resources\AnimalTypes;

use App\Filament\Resources\AnimalTypes\Pages\CreateAnimalType;
use App\Filament\Resources\AnimalTypes\Pages\EditAnimalType;
use App\Filament\Resources\AnimalTypes\Pages\ListAnimalTypes;
use App\Filament\Resources\AnimalTypes\Schemas\AnimalTypeForm;
use App\Filament\Resources\AnimalTypes\Tables\AnimalTypesTable;
use App\Models\AnimalType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnimalTypeResource extends Resource
{
    protected static ?string $model = AnimalType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AnimalTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnimalTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnimalTypes::route('/'),
            'create' => CreateAnimalType::route('/create'),
            'edit' => EditAnimalType::route('/{record}/edit'),
        ];
    }
}
