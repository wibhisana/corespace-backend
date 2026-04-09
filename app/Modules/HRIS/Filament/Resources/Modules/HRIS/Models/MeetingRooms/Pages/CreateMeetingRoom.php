<?php
namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\MeetingRooms\Pages;
use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\MeetingRooms\MeetingRoomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeetingRoom extends CreateRecord {
    protected static string $resource = MeetingRoomResource::class;
}
