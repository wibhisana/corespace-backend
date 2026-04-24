<?php

namespace App\Modules\HRIS\Filament\Resources\MeetingRooms;

use App\Models\MeetingRoom;
use App\Modules\HRIS\Filament\Resources\MeetingRooms\Pages\CreateMeetingRoom;
use App\Modules\HRIS\Filament\Resources\MeetingRooms\Pages\EditMeetingRoom;
use App\Modules\HRIS\Filament\Resources\MeetingRooms\Pages\ListMeetingRooms;
use App\Modules\HRIS\Filament\Resources\MeetingRooms\Schemas\MeetingRoomForm;
use App\Modules\HRIS\Filament\Resources\MeetingRooms\Tables\MeetingRoomsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MeetingRoomResource extends Resource
{
    protected static ?string $model = MeetingRoom::class;

    // Ikon pintu/ruangan untuk menu
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    // Label di menu sidebar
    protected static ?string $navigationLabel = 'Ruang Rapat';
    protected static ?string $modelLabel = 'Ruang Rapat';

    public static function form(Schema $schema): Schema
    {
        return MeetingRoomForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MeetingRoomsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMeetingRooms::route('/'),
            'create' => CreateMeetingRoom::route('/create'),
            'edit' => EditMeetingRoom::route('/{record}/edit'),
        ];
    }
}
