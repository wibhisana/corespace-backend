<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoomBooking;
use Illuminate\Http\Request;

class RoomBookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'purpose' => 'required|string|max:500',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        // Cek bentrok: apakah ada booking lain di ruangan & waktu yang sama
        $conflict = RoomBooking::where('meeting_room_id', $request->meeting_room_id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($request) {
                $q->where('start_time', '<', $request->end_time)
                  ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Ruangan sudah dibooking di jam tersebut. Silakan pilih waktu lain.',
            ], 422);
        }

        $booking = RoomBooking::create([
            'user_id' => $request->user()->id,
            'meeting_room_id' => $request->meeting_room_id,
            'purpose' => $request->purpose,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->meetingRoom?->requires_approval ? 'pending' : 'approved',
        ]);

        $booking->load('meetingRoom.location');

        return response()->json([
            'message' => 'Booking berhasil.',
            'data' => $booking,
        ], 201);
    }
}
