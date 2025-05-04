<?php

use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::middleware(['role:admin'])->group(function () {
        /* Hotel Routes */
        Route::get('hotels/{hotel}/edit', [HotelController::class, 'showEditHotel']);
        Route::get('hotels', [HotelController::class, 'showAllHotelsList']);
        Route::post('hotels', [HotelController::class, 'storeHotel']);
        Route::post('hotels/update', [HotelController::class, 'updateHotel']);
        Route::delete('hotels/{hotel}/delete', [HotelController::class, 'destroyHotel']);

        /* Room Routes */
        Route::get('rooms', [RoomController::class, 'showAllRoomsList']);
        Route::post('rooms', [RoomController::class, 'storeRooms']);
        Route::get('rooms/{room}/edit', [RoomController::class, 'showEditRoom']);
        Route::post('rooms/update', [RoomController::class, 'updateRoom']);
        Route::patch('rooms/{room}', [RoomController::class, 'update']);
        Route::delete('rooms/{room}', [RoomController::class, 'deleteRoom']);
    });


    Route::middleware(['role:user'])->group(function () {

        Route::post('bookings', [BookingController::class, 'bookingRooms']);
        Route::get('hotels/search', [BookingController::class, 'search']);
        Route::get('/bookings/history', [BookingController::class, 'bookingHistory']);
        Route::delete('/bookings/{id}/cancel', [BookingController::class, 'cancelBooking']);
    });
});
