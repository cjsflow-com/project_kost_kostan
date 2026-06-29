<?php

namespace App\Filament\Resources\RoomFacilities\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RoomFacilitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('icon')
                    ->formatStateUsing(fn (?string $state): string => $state ? 'Lihat Icon' : 'Belum Ada Icon')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'success' : 'danger')
                    ->searchable()
                    ->action(
                        Action::make('lihatIcon')
                            ->label('Lihat Icon')
                            ->modalHeading(fn ($record) => 'Icon: ' . $record->name)
                            ->modalWidth(Width::Large)
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup')
                            ->disabled(fn ($record): bool => blank($record->icon))
                            ->schema([
                                ImageEntry::make('icon')
                                    ->label('Preview Icon')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->imageHeight(300)
                                    ->extraImgAttributes([
                                        'class' => 'object-contain rounded-lg',
                                    ]),
                            ])
                    ),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
