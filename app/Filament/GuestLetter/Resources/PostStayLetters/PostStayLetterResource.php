<?php

namespace App\Filament\GuestLetter\Resources\PostStayLetters;

use App\Filament\GuestLetter\Resources\PostStayLetters\Pages\CreatePostStayLetter;
use App\Filament\GuestLetter\Resources\PostStayLetters\Pages\ListPostStayLetters;
use App\Filament\GuestLetter\Resources\PostStayLetters\Schemas\PostStayLetterForm;
use App\Filament\GuestLetter\Resources\PostStayLetters\Tables\PostStayLettersTable;
use App\Modules\GuestLetter\Models\GuestLetterSend;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PostStayLetterResource extends Resource
{
    protected static ?string $model = GuestLetterSend::class;
    protected static bool $isDiscovered = false;

    // protected static string|UnitEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;
    protected static ?string $navigationLabel = 'Send Post-Stay Letter';
    protected static ?string $modelLabel = 'Post-stay Letter Send';
    protected static ?string $pluralModelLabel = 'Post-stay Letter Sends';
    protected static string | UnitEnum | null $navigationGroup = 'Post Stay Letters';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return PostStayLetterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostStayLettersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPostStayLetters::route('/'),
            'create' => CreatePostStayLetter::route('/create'),
        ];
    }
}
