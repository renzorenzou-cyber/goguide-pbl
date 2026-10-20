<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (Review::exists()) {
            return;
        }

        $tours = Tour::all();
        if ($tours->isEmpty()) {
            return;
        }

        $samples = [
            ['Dewi Anggraini', 5, 'Guide-nya ramah banget dan hafal spot-spot sepi. Kami dibawa ke tempat yang nggak ada di brosur wisata.'],
            ['Rizky Pratama', 5, 'Penjelasan soal sejarah dan upacara adatnya detail tapi santai. Worth it banget buat keluarga.'],
            ['Sarah Mitchell', 4, 'Great local insight and very flexible with our schedule. Would book again.'],
            ['Budi Santoso', 5, 'Jemputan tepat waktu, rute nggak buru-buru, dan banyak spot foto bagus.'],
            ['Anita Wijaya', 4, 'Seru dan informatif. Cuma agak panas siang hari, jadi bawa topi dan air minum.'],
            ['Lucas Meyer', 5, 'Best day of our Bali trip. The guide knew exactly when to arrive to beat the crowds.'],
            ['Putri Maharani', 4, 'Guide sabar banget sama anak-anak kami. Itinerary-nya pas, nggak terlalu padat.'],
            ['Hendra Kusuma', 5, 'Dibawa makan di warung lokal yang enak dan murah. Pengalamannya autentik.'],
            ['Emma Johansson', 5, 'Friendly, punctual, and full of stories about the local culture. Highly recommended.'],
            ['Maya Salsabila', 4, 'Tour-nya seru dan guide-nya komunikatif. Sedikit telat mulai, tapi terbayar pengalamannya.'],
        ];

        foreach ($samples as $i => [$name, $rating, $comment]) {
            $tour = $tours[$i % $tours->count()];

            $review = new Review([
                'reviewer_name' => $name,
                'tour_id'       => $tour->id,
                'guide_id'      => $tour->guide_id,
                'rating'        => $rating,
                'comment'       => $comment,
            ]);
            $review->created_at = now()->subDays($i * 4 + 2);
            $review->save();
        }
    }
}