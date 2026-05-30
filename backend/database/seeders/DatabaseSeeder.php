<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Spot;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'Admin SpotList',
            'email'    => 'admin@spotlist.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // Member
        User::create([
            'name'     => 'Budi Member',
            'email'    => 'budi@spotlist.com',
            'password' => Hash::make('password123'),
            'role'     => 'member',
        ]);

        // Spots
        $spots = [
            [
                'title'             => 'Surabaya Night Market Festival',
                'thumbnail'         => 'placeholder.jpg',
                'description'       => 'Festival kuliner malam hari dengan 100+ tenant makanan lokal dan live music.',
                'category'          => 'Festival',
                'location'          => 'Taman Bungkul, Surabaya',
                'event_date'        => '2025-07-15 18:00:00',
                'organizer'         => 'Dinas Pariwisata Surabaya',
                'price'             => 0,
                'participant_limit' => 500,
                'registration_link' => 'https://example.com/nightmarket',
                'status'            => 'open',
            ],
            [
                'title'             => 'Workshop Fotografi Urban',
                'thumbnail'         => 'placeholder.jpg',
                'description'       => 'Belajar teknik fotografi jalanan bersama fotografer profesional.',
                'category'          => 'Workshop',
                'location'          => 'Kota Lama, Surabaya',
                'event_date'        => '2025-07-20 09:00:00',
                'organizer'         => 'Komunitas Foto SBY',
                'price'             => 75000,
                'participant_limit' => 30,
                'registration_link' => 'https://example.com/fotografi',
                'status'            => 'open',
            ],
            [
                'title'             => 'Open Trip Gunung Penanggungan',
                'thumbnail'         => 'placeholder.jpg',
                'description'       => 'Open trip hiking untuk pemula dengan pemandu berpengalaman.',
                'category'          => 'Community Meetup',  // changed
                'location'          => 'Gunung Penanggungan, Mojokerto',
                'event_date'        => '2025-08-03 04:00:00',
                'organizer'         => 'Alam Bebas Community',
                'price'             => 150000,
                'participant_limit' => 20,
                'registration_link' => 'https://example.com/hiking',
                'status'            => 'open',
            ],
            [
                'title'             => 'Pameran Seni Kontemporer',
                'thumbnail'         => 'placeholder.jpg',
                'description'       => 'Pameran karya seniman muda Surabaya dalam format pop-up gallery.',
                'category'          => 'Exhibition',        // changed
                'location'          => 'House of Sampoerna, Surabaya',
                'event_date'        => '2025-07-25 10:00:00',
                'organizer'         => 'Galeri Muda SBY',
                'price'             => 25000,
                'participant_limit' => 200,
                'registration_link' => 'https://example.com/pameran',
                'status'            => 'closed',
            ],
            [
                'title'             => 'Coding Bootcamp Weekend',
                'thumbnail'         => 'placeholder.jpg',
                'description'       => 'Belajar web development dari nol dalam 2 hari intensif.',
                'category'          => 'Workshop',          // changed
                'location'          => 'Dyandra Convention, Surabaya',
                'event_date'        => '2025-08-10 08:00:00',
                'organizer'         => 'Tech Community Surabaya',
                'price'             => 200000,
                'participant_limit' => 50,
                'registration_link' => 'https://example.com/bootcamp',
                'status'            => 'draft',
            ],
        ];

        foreach ($spots as $spot) {
            Spot::create($spot);
        }
    }
}
