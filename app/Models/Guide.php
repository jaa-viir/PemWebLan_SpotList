<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // Import Attribute untuk Accessor

class Guide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 
        'slug', 
        'banner_image', 
        'content', 
        'user_id', 
    ];

    /**
     * Relasi ke User (Penulis/Admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk mempermudah pemanggilan URL gambar di Flutter/Frontend
     */
    public function bannerImage(): Attribute // Nama fungsi disesuaikan dengan nama kolom (camelCase)
    {
        return Attribute::make(
            get: fn ($image) => $image ? url('/storage/guides/' . $image) : null,
        );
    }
}