<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Spot;
use App\Models\Guide;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed/Update Admin
        $admin = User::updateOrCreate(
            ['email'    => 'admin@spotlist.com'],
            [
                'name'     => 'Admin SpotList',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        // 2. Seed/Update Member
        User::updateOrCreate(
            ['email'    => 'budi@spotlist.com'],
            [
                'name'     => 'Budi Member',
                'password' => Hash::make('password123'),
                'role'     => 'member',
            ]
        );

        // 3. Dataset Spots
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
                'category'          => 'Community Meetup',
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
                'category'          => 'Exhibition',
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
                'category'          => 'Workshop',
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
            Spot::updateOrCreate(
                ['title' => $spot['title']],
                $spot
            );
        }

        // 4. Dataset Guides (Fitur Bagianmu)
        $guides = [
            [
                'user_id'      => $admin->id,
                'title'        => 'Panduan Lengkap Menikmati Surabaya Night Market',
                'slug'         => 'panduan-lengkap-menikmati-surabaya-night-market',
                'content'      => "1. Datanglah lebih awal sekitar jam 5 sore untuk menghindari macet dan antrean parkir.\n2. Siapkan uang tunai pecahan kecil karena beberapa tenant belum mendukung QRIS.\n3. Coba menu andalan seperti Semanggi Suroboyo dan Lontong Balap terkurasi di zona B.",
                'banner_image' => 'placeholder.jpg',
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'Persiapan Sebelum Ikut Open Trip Hiking Pemula',
                'slug'         => 'persiapan-sebelum-ikut-open-trip-hiking-pemula',
                'content'      => "Persiapan fisik sangat krusial sebelum mendaki. Lakukan jogging 3 kali seminggu sebelum hari H.\nBarang bawaan wajib:\n- Jas hujan plastik\n- Sepatu trekking dengan grip yang baik\n- Air minum minimal 2 liter\n- Obat-obatan pribadi.",
                'banner_image' => 'placeholder.jpg',
            ]
        ];

        foreach ($guides as $guide) {
            Guide::updateOrCreate(
                ['title' => $guide['title']],
                $guide
            );
        }
    }
}