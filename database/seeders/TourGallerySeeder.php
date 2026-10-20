<?php

namespace Database\Seeders;

use App\Models\Guide;
use App\Models\Tour;
use App\Models\TourImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TourGallerySeeder extends Seeder
{
    private const EXT = ['jpg', 'jpeg', 'png', 'webp'];

    // Profil awal guide, GANTI sesuai data asli.
    private array $guideProfiles = [
        'Ayu Lestari' => [
            'bio' => "Halo, aku Ayu! Aku lahir dan besar di Ubud, jadi aku kenal betul pura, desa, dan sawah di sini.\nDi tour aku, kamu nggak cuma lihat tempatnya, tapi juga dengar cerita dan tradisi di baliknya.",
            'experience_years' => 6,
            'base_location' => 'Ubud, Gianyar',
        ],
        'Made Wirawan' => [
            'bio' => "Aku Made, suka banget trekking dan air terjun. Aku bakal pilihin jalur yang aman dan pas buat kemampuan kamu.",
            'experience_years' => 5,
            'base_location' => 'Gianyar, Bali',
        ],
    ];

    public function run(): void
    {
        $this->seedTourImages();
        $this->seedGuides();
    }

    private function seedTourImages(): void
    {
        foreach (Tour::all() as $tour) {
            $dir = public_path('images/tours/' . $tour->slug);

            if (! File::isDirectory($dir)) {
                $this->command?->warn("Lewat: folder public/images/tours/{$tour->slug}/ belum ada.");
                continue;
            }

            $files = collect(File::files($dir))
                ->filter(fn ($f) => in_array(strtolower($f->getExtension()), self::EXT, true))
                ->sortBy(fn ($f) => $f->getFilename(), SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            foreach ($files as $i => $file) {
                $path = 'images/tours/' . $tour->slug . '/' . $file->getFilename();

                TourImage::updateOrCreate(
                    ['tour_id' => $tour->id, 'path' => $path],
                    ['caption' => $this->captionFrom($file->getFilenameWithoutExtension()), 'sort_order' => $i + 1]
                );
            }

            $this->command?->info("{$tour->title}: {$files->count()} foto.");
        }
    }

    private function seedGuides(): void
    {
        foreach (Guide::with('user')->get() as $guide) {
            $name = $guide->user->name;
            $data = $this->guideProfiles[$name] ?? [];

            // Foto guide: public/images/guides/{slug-nama}.(jpg|jpeg|png|webp)
            foreach (self::EXT as $ext) {
                $rel = 'images/guides/' . Str::slug($name) . '.' . $ext;
                if (File::exists(public_path($rel))) {
                    $data['photo'] = $rel;
                    break;
                }
            }

            if ($data) {
                $guide->update($data);
                $this->command?->info("Guide {$name} diperbarui: " . implode(', ', array_keys($data)));
            }
        }
    }

    private function captionFrom(string $filename): string
    {
        $text = preg_replace('/^[\d\s._-]+/', '', $filename);   // buang nomor urut di depan
        $text = trim(str_replace(['-', '_'], ' ', $text));

        return $text === '' ? '' : Str::ucfirst($text);
    }
}