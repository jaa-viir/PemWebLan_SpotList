<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = ['user_id', 'spot_id', 'status'];

    public function spot()
    {
        return $this->belongsTo(Spot::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
