<?php

namespace App\Filament\Resources\ForestUsers;

use App\Filament\Resources\ForestUsers\Pages\CreateForestUser;
use App\Filament\Resources\ForestUsers\Pages\EditForestUser;
use App\Filament\Resources\ForestUsers\Pages\ListForestUsers;
use App\Filament\Resources\ForestUsers\Schemas\ForestUserForm;
use App\Filament\Resources\ForestUsers\Tables\ForestUsersTable;
use App\Models\ForestUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ForestUserResource extends Resource
{
    protected static ?string $model = ForestUser::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ForestUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForestUsersTable::configure($table);
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
            'index' => ListForestUsers::route('/'),
            'create' => CreateForestUser::route('/create'),
            'edit' => EditForestUser::route('/{record}/edit'),
        ];
    }
}
