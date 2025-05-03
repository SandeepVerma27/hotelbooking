<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    /**
     * List all hotels.
     * @return JsonResponse
     */
    public function showAllHotelsList()
    {
        try {
            /* Get all hotels */
            $hotels = Hotel::with('createdBy')->get();

            return response()->json([
                'status' => true,
                'message' => 'Hotels retrieved successfully',
                'data' => $hotels
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve hotels',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created hotel in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */

    public function storeHotel(Request $request)
    {
        /* Validate the request */
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'description' => 'nullable|string',
                'hotel_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5096',
                'contact_number' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:255',
                'is_active' => 'nullable|boolean',
                'is_featured' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            /* Check if the hotel already exists */
            $checkHotel = Hotel::where('name', $request->name)->first();

            if ($checkHotel) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hotel already exists',
                ], 409);
            }

            $data = $validator->validated();

            /* Handle hotel image upload */
            if ($request->hasFile('hotel_image')) {
                $uploadPath = public_path('upload/hotel_image');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $file = $request->file('hotel_image');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

                $file->move($uploadPath, $fileName);
                $data['hotel_image'] = 'upload/hotel_image/' . $fileName;
            }

            /* Create a new hotel */
            $data['created_by'] = auth()->id();
            $hotel = Hotel::create($data);

            /* load creator name */
            $hotel->load('createdBy');
            return response()->json([
                'status' => true,
                'message' => 'Hotel created successfully',
                'data' => [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'location' => $hotel->location,
                    'description' => $hotel->description,
                    'hotel_image' => $hotel->hotel_image,
                    'created_by' => $hotel->createdBy->name,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create hotel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified hotel.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function showEditHotel($id)
    {
        try {
            /* Check if the hotel exists */
            $hotel = Hotel::find($id);

            if (!$hotel) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hotel not found',
                ], 404);
            }

            /* load creator name */
            $hotel->load('createdBy');

            return response()->json([
                'status' => true,
                'message' => 'Hotel retrieved successfully',
                'data' => array_merge(
                    $hotel->toArray(),
                    ['created_by' => $hotel->createdBy->name]
                )
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve hotel',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update the specified hotel in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */

    public function updateHotel(Request $request)
    {
        /* Validate the request */
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'description' => 'nullable|string',
                'hotel_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5096',
                'contact_number' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:255',
                'is_active' => 'nullable|boolean',
                'is_featured' => 'nullable|boolean',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            /* Check if the hotel exists */
            $hotel = Hotel::find($request->id);

            if (!$hotel) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hotel not found',
                ], 404);
            }

            $data = $validator->validated();

            /* Handle hotel image upload */
            if ($request->hasFile('hotel_image')) {
                if ($hotel->hotel_image && file_exists(public_path('storage/' . $hotel->hotel_image))) {
                    unlink(public_path('storage/' . $hotel->hotel_image));
                }
                $imagePath = $request->file('hotel_image')->store('hotel_images', 'public');
                $data['hotel_image'] = $imagePath;
            }

            $hotel->update($data);
            return response()->json([
                'status' => true,
                'message' => 'Hotel updated successfully',
                'data' => [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'location' => $hotel->location,
                    'description' => $hotel->description,
                    'created_by' => $hotel->createdBy->name,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update hotel',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified hotel from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroyHotel($id)
    {
        try {
            /* Check if the hotel exists */
            $hotel = Hotel::find($id);

            if (!$hotel) {
                return response()->json([
                    'status' => false,
                    'message' => 'Hotel not found',
                ], 404);
            }

            /* Delete the hotel */
            $hotel->delete();

            return response()->json([
                'status' => true,
                'message' => 'Hotel deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete hotel',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
