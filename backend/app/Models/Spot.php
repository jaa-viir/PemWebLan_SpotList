<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Spot extends Model
{
    protected $fillable = [
        'title',
        'thumbnail',
        'description',
        'category',
        'location',
        'event_date',
        'organizer',
        'price',
        'participant_limit',
        'registration_link',
        'status'
    ];

    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: fn($value) => url('/storage/spots/' . $value),
        );
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
