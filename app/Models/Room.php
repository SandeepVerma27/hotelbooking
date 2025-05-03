<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_number',
        'room_type',
        'price_per_night',
        'max_guests',
        'is_available',
        'is_active',
        'is_featured',
        'description',
        'size',
        'room_image',
        'amenities',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_id');
    }
}
