<?php

namespace Database\Seeders;

use App\Models\{Guide, Region, Tour, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BaliSeeder extends Seeder
{
    public function run(): void
    {
        $regionNames = ['Ubud','Canggu','Kuta','Uluwatu','Nusa Penida','Sanur'];
        $regions = [];
        foreach ($regionNames as $name) {
            $regions[$name] = Region::create(['name' => $name, 'slug' => Str::slug($name)]);
        }

        $guideData = [
            ['Ayu Lestari','Bali Culture & Nature','Indonesia, English'],
            ['Made Wirawan','Waterfall & Trekking','Indonesia, English'],
        ];

        $guides = [];
        foreach ($guideData as $i => [$name, $specialty, $lang]) {
            $user = User::create([
                'name' => $name,
                'email' => 'guide'.($i+1).'@goguide.test',
                'password' => Hash::make('password'),
                'role' => 'guide',
            ]);
            $guides[] = Guide::create([
                'user_id' => $user->id,
                'specialty' => $specialty,
                'languages' => $lang,
                'bio' => 'Local guide berpengalaman di Bali.',
                'rating' => 4.9,
                'review_count' => 120,
            ]);
        }

        $tours = [
            ['Ubud Temple & Rice Terrace Walk','Ubud',450000,'6 jam'],
            ['Tegenungan Waterfall Adventure','Ubud',350000,'5 jam'],
            ['Canggu Sunset & Surf Culture','Canggu',400000,'4 jam'],
            ['Uluwatu Temple & Kecak Dance','Uluwatu',450000,'5 jam'],
            ['Nusa Penida Day Trip','Nusa Penida',650000,'10 jam'],
            ['Sanur Heritage Cycling','Sanur',300000,'4 jam'],
            ['Kuta Beach & Local Food Tour','Kuta',280000,'4 jam'],
        ];

        foreach ($tours as $i => [$title, $region, $price, $duration]) {
            Tour::create([
                'region_id' => $regions[$region]->id,
                'guide_id' => $guides[$i % 2]->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => 'Jelajahi '.$region.' bareng guide lokal bersertifikat.',
                'price' => $price,
                'duration' => $duration,
                'rating' => 4.8,
                'review_count' => 80,
            ]);
        }
    }
}