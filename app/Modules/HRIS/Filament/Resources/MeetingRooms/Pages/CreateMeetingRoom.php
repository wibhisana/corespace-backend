<?php
namespace App\Modules\HRIS\Filament\Resources\MeetingRooms\Pages;
use App\Modules\HRIS\Filament\Resources\MeetingRooms\MeetingRoomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeetingRoom extends CreateRecord {
    protected static string $resource = MeetingRoomResource::class;
}
