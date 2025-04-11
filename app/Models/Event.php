<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
use HasFactory;

protected $fillable = [
'title', 'description', 'start_date', 'end_date',
'location', 'capacity', 'price'
];

public function bookings()
{
return $this->hasMany(Booking::class);
}

public function availableSeats()
{
return $this->capacity - $this->bookings()->sum('ticket_count');
}
}
?>