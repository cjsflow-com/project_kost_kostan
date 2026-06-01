<?php

namespace App\Filament\Resources\RoomFacilities;

use App\Filament\Resources\RoomFacilities\Pages\CreateRoomFacility;
use App\Filament\Resources\RoomFacilities\Pages\EditRoomFacility;
use App\Filament\Resources\RoomFacilities\Pages\ListRoomFacilities;
use App\Filament\Resources\RoomFacilities\Pages\ViewRoomFacility;
use App\Filament\Resources\RoomFacilities\Schemas\RoomFacilityForm;
use App\Filament\Resources\RoomFacilities\Schemas\RoomFacilityInfolist;
use App\Filament\Resources\RoomFacilities\Tables\RoomFacilitiesTable;
use App\Models\RoomFacility;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RoomFacilityResource extends Resource
{
    protected static ?string $model = RoomFacility::class;


    protected static ?string $recordTitleAttribute = 'room_facility';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Fasilitas Kamar';

    public static function form(Schema $schema): Schema
    {
        return RoomFacilityForm::configure($schema);
    }

     public static function getNavigationGroup(): ?string
    {
        return 'Kelola Kamar';
    }

    public static function infolist(Schema $schema): Schema
    {
        return RoomFacilityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoomFacilitiesTable::configure($table);
    }

    public static function getNavigationIcon(): string
    {
         return 'fas-bed';
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
            'index' => ListRoomFacilities::route('/'),
            'create' => CreateRoomFacility::route('/create'),
            'view' => ViewRoomFacility::route('/{record}'),
            'edit' => EditRoomFacility::route('/{record}/edit'),
        ];
    }
}
