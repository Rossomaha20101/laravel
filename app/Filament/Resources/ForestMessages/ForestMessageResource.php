<?php

namespace App\Filament\Resources\ForestMessages;

use App\Filament\Resources\ForestMessages\Pages\CreateForestMessage;
use App\Filament\Resources\ForestMessages\Pages\EditForestMessage;
use App\Filament\Resources\ForestMessages\Pages\ListForestMessages;
use App\Filament\Resources\ForestMessages\Schemas\ForestMessageForm;
use App\Filament\Resources\ForestMessages\Tables\ForestMessagesTable;
use App\Models\ForestMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ForestMessageResource extends Resource
{
    protected static ?string $model = ForestMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ForestMessageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForestMessagesTable::configure($table);
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
            'index' => ListForestMessages::route('/'),
            'create' => CreateForestMessage::route('/create'),
            'edit' => EditForestMessage::route('/{record}/edit'),
        ];
    }
}
