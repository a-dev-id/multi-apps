<?php

namespace App\Filament\Newsletter\Resources\Subscribers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscribersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with('tags')) // important
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),

                TextColumn::make('tags_list')
                    ->label('Tags')
                    ->state(fn($record) => $record->tags?->pluck('name')->join(', ') ?: '-')
                    ->wrap()
                    ->toggleable()
                    ->searchable(
                        query: function (Builder $query, string $search): Builder {
                            return $query->whereHas('tags', function (Builder $q) use ($search) {
                                $q->where('name', 'like', "%{$search}%");
                            });
                        }
                    ),


                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Is active')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
