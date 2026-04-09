<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomBooking extends Model
{
    protected $fillable = ['user_id', 'meeting_room_id', 'purpose', 'start_time', 'end_time', 'status'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function meetingRoom() { return $this->belongsTo(MeetingRoom::class); }
}
