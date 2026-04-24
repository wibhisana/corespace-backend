<?php
namespace App\Modules\HRIS\Filament\Resources\MeetingRooms\Pages;
use App\Modules\HRIS\Filament\Resources\MeetingRooms\MeetingRoomResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMeetingRoom extends EditRecord {
    protected static string $resource = MeetingRoomResource::class;
    protected function getHeaderActions(): array {
        return [DeleteAction::make()];
    }
}
