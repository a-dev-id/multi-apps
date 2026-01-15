<?php

namespace App\Filament\GuestLetter\Resources\Bookings;

use App\Filament\GuestLetter\Resources\Bookings\Pages\CreateBooking;
use App\Filament\GuestLetter\Resources\Bookings\Pages\EditBooking;
use App\Filament\GuestLetter\Resources\Bookings\Pages\ListBookings;
use App\Filament\GuestLetter\Resources\Bookings\Schemas\BookingForm;
use App\Filament\GuestLetter\Resources\Bookings\Tables\BookingsTable;
use App\Modules\GuestLetter\Models\Booking;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

use App\Filament\GuestLetter\Resources\Bookings\RelationManagers\GuestLetterSendsRelationManager;
use App\Filament\GuestLetter\Resources\Bookings\Pages\ViewBooking;


class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    public static function form(Schema $schema): Schema
    {
        return BookingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            GuestLetterSendsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
            'create' => CreateBooking::route('/create'),
            'edit' => EditBooking::route('/{record}/edit'),
            'view'   => ViewBooking::route('/{record}'),
        ];
    }
}
