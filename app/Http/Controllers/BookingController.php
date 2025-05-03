<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\HotelController;
use App\Models\Booking;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{


    /**
     * Book a room.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bookingRooms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $overlap = Booking::where('room_id', $data['room_id'])
            ->where('status', 'confirmed')
            ->where(function ($q) use ($data) {
                $q->whereBetween('check_in_date', [$data['check_in_date'], $data['check_out_date']])
                    ->orWhereBetween('check_out_date', [$data['check_in_date'], $data['check_out_date']])
                    ->orWhereRaw('? BETWEEN check_in_date AND check_out_date', [$data['check_in_date']])
                    ->orWhereRaw('? BETWEEN check_in_date AND check_out_date', [$data['check_out_date']]);
            })->exists();

        if ($overlap) {
            return response()->json([
                'status' => false,
                'message' => 'Room not available for selected dates'
            ], 409);
        }

        // Create the booking
        $booking = Booking::create([
            ...$data,
            'user_id' => auth()->id(),
            'status' => 'confirmed'
        ]);

        // Eager load the related room and hotel data
        $booking->load('user', 'room.hotel');

        // Return the booking details with user and room data
        return response()->json([
            'status' => true,
            'message' => 'Booking confirmed',
            'data' => [
                'booking_id' => $booking->id,
                'check_in_date' => $booking->check_in_date,
                'check_out_date' => $booking->check_out_date,
                'status' => $booking->status,
                'user_name' => $booking->user->name,
                'room_id' => $booking->room->id,
                'room_number' => $booking->room->room_number,
                'room' => [
                    'room_type' => $booking->room->room_type,
                    'price_per_night' => $booking->room->price_per_night,
                    'max_guests' => $booking->room->max_guests,
                ],
                'hotel' => [
                    'name' => $booking->room->hotel->name,
                    'location' => $booking->room->hotel->location,
                    'description' => $booking->room->hotel->description,
                ]
            ]
        ], 201);
    }


    /**
     * Apply the filter on hotel.
     *
     * @return JsonResponse
     */


    public function search(Request $request)
    {
        $location = $request->query('location');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $checkIn = $request->query('check_in');
        $checkOut = $request->query('check_out');

        $hotels = Hotel::with(['rooms' => function ($query) use ($minPrice, $maxPrice, $checkIn, $checkOut) {
            // Filter by price range
            if ($minPrice !== null && $maxPrice !== null) {
                $query->whereBetween('price_per_night', [$minPrice, $maxPrice]);
            }

            // Filter out rooms that are booked in this range
            if ($checkIn && $checkOut) {
                $query->whereDoesntHave('bookings', function ($bookingQuery) use ($checkIn, $checkOut) {
                    $bookingQuery->where('status', 'confirmed')
                        ->where(function ($q) use ($checkIn, $checkOut) {
                            $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                                ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                                ->orWhereRaw('? BETWEEN check_in_date AND check_out_date', [$checkIn])
                                ->orWhereRaw('? BETWEEN check_in_date AND check_out_date', [$checkOut]);
                        });
                });
            }
        }])
            ->when($location, function ($query) use ($location) {
                $query->where('location', 'like', "%{$location}%");
            })
            ->whereHas('rooms') // only hotels with rooms
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Hotels found',
            'data' => $hotels
        ]);
    }


    public function bookingHistory()
    {
        $user = auth()->user();

        $bookings = Booking::with(['room', 'room.hotel', 'user'])
            ->where('user_id', $user->id)
            ->orderByDesc('check_in_date')
            ->get();

        $userName = $user->name;

        return response()->json([
            'status' => true,
            'message' => 'Booking history retrieved successfully',
            'data'=>$bookings

        ], 200);
    }


    public function cancelBooking($id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$booking) {
            return response()->json([
                'status' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        if ($booking->check_in_date <= now()->toDateString()) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot cancel past or current bookings'
            ], 400);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'status' => true,
            'message' => 'Booking cancelled successfully'
        ]);
    }
}
