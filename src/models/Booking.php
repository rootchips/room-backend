<?php
use Illuminate\Database\Eloquent\Model;

class Booking extends Model {
    protected $table = 'bookings';
    protected $fillable = ['user_id', 'room_id', 'booking_date', 'status'];
}
