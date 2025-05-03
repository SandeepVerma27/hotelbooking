<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'hotel_image',
        'description',
        'contact_number',
        'email',
        'is_active',
        'is_featured',
        'created_by'
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function admin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
