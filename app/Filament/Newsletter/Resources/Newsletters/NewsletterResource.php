<?php

namespace App\Filament\Newsletter\Resources\Newsletters;

use App\Filament\Newsletter\Resources\Newsletters\Pages\CreateNewsletter;
use App\Filament\Newsletter\Resources\Newsletters\Pages\EditNewsletter;
use App\Filament\Newsletter\Resources\Newsletters\Pages\ListNewsletters;
use App\Filament\Newsletter\Resources\Newsletters\Schemas\NewsletterForm;
use App\Filament\Newsletter\Resources\Newsletters\Tables\NewslettersTable;
use App\Modules\Newsletter\Models\Newsletter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    public static function form(Schema $schema): Schema
    {
        return NewsletterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewslettersTable::configure($table);
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
            'index' => ListNewsletters::route('/'),
            'create' => CreateNewsletter::route('/create'),
            'edit' => EditNewsletter::route('/{record}/edit'),
        ];
    }
}
