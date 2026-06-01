<?php

namespace App\Filament\Resources\RoomFacilities\Pages;

use App\Filament\Resources\RoomFacilities\RoomFacilityResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRoomFacility extends ViewRecord
{
    protected static string $resource = RoomFacilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
