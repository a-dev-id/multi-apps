<?php

namespace App\Filament\Newsletter\Resources\Tags;

use App\Filament\Newsletter\Resources\Tags\Pages\CreateTag;
use App\Filament\Newsletter\Resources\Tags\Pages\EditTag;
use App\Filament\Newsletter\Resources\Tags\Pages\ListTags;
use App\Filament\Newsletter\Resources\Tags\Schemas\TagForm;
use App\Filament\Newsletter\Resources\Tags\Tables\TagsTable;
use App\Modules\Newsletter\Models\Tag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Filament\Schemas\Schema;
use Filament\Forms;
use Illuminate\Support\Str;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')
                ->label('Tag Name')
                ->required()
                ->live(onBlur: true)
                ->maxLength(255)
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    // Only auto-generate if slug is empty or unchanged
                    if (blank($get('slug'))) {
                        $set('slug', Str::slug($state));
                    }
                }),

            Forms\Components\TextInput::make('slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('Auto-generated from name. You can edit it if needed.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
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
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }
}
