<?php

namespace App\Filament\GuestLetter\Resources\Rooms\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(150),

            FileUpload::make('image')
                ->image()
                ->directory('rooms')
                ->imageEditor()
                ->maxSize(2048),

            RichEditor::make('description')
                ->columnSpanFull()
                ->extraInputAttributes([
                    'class' => 'min-h-[600px]',
                ]),

            Toggle::make('is_active')
                ->default(true),
        ])->columns(2);
    }
}
