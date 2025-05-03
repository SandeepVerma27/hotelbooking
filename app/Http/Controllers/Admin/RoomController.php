<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\RoomImage;

class RoomController extends Controller
{

    /**
     * List all rooms.
     *
     * @return JsonResponse
     */

    public function showAllRoomsList()
    {
        try {
            $rooms = Room::with('hotel')->get();

            return response()->json([
                'status' => true,
                'message' => 'Rooms retrieved successfully',
                'data' => $rooms
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve rooms',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created room.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function storeRooms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hotels,id',
            'room_number' => 'required|string|max:20',
            'room_type' => 'required|string|max:50',
            'price_per_night' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'is_available' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'description' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'amenities' => 'nullable|string|max:255',
            'room_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();

            // Handle image upload if present
            if ($request->hasFile('room_image')) {
                $uploadPath = public_path('upload/room_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $file = $request->file('room_image');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $fileName);

                $data['room_image'] = 'upload/room_images/' . $fileName;
            }

            // Create room with image path stored directly
            $room = Room::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Room created successfully',
                'data' => $room
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create room',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    /**
     * Show the specified room.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function showEditRoom($id)
    {

        try {
            $room = Room::with('hotel')->where('id', $id)->get();
            return response()->json([
                'status' => true,
                'message' => 'Room retrieved successfully',
                'data' => $room
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve room',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update the specified room.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hotels,id',
            'room_number' => 'required|string|max:20',
            'room_type' => 'required|string|max:50',
            'price_per_night' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'is_available' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'description' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'amenities' => 'nullable|string|max:255',
            'room_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();

            $room = Room::findOrFail($request->id);

            // Handle image upload if present
            if ($request->hasFile('room_image')) {
                $uploadPath = public_path('upload/room_images');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $file = $request->file('room_image');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $fileName);

                $data['room_image'] = 'upload/room_images/' . $fileName;

                // Delete the old image if it exists
                if ($room->room_image && file_exists(public_path($room->room_image))) {
                    unlink(public_path($room->room_image));
                }
            }

            // Update room with new data
            $room->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Room updated successfully',
                'data' => $room
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified room.
     *
     * @param int $id
     * @return JsonResponse
     */

    public function deleteRoom($id)
    {
        try {
            $room = Room::findOrFail($id);

            // Delete the room image if it exists
            if ($room->room_image && file_exists(public_path($room->room_image))) {
                unlink(public_path($room->room_image));
            }

            $room->delete();

            return response()->json([
                'status' => true,
                'message' => 'Room deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete room',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
