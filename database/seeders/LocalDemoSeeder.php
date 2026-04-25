<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\ContentFeature;
use App\Models\Event;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class LocalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->copyDemoImages();

        $contents = [
            [
                'name' => 'Menara Teratai',
                'slug' => 'menara-teratai',
                'description' => 'Landmark Purwokerto dengan panorama kota dan Gunung Slamet.',
                'price_weekday' => '15000',
                'price_weekend' => '20000',
                'open_time' => '08:00',
                'close_time' => '21:00',
                'location' => 'Purwokerto, Banyumas',
                'image' => 'assets/img/menara.jpg',
                'instagram' => 'https://instagram.com/bludpariwisata',
                'tiktok' => null,
            ],
            [
                'name' => 'Taman Mas Kemambang',
                'slug' => 'taman-mas-kemambang',
                'description' => 'Ruang rekreasi keluarga dengan suasana taman dan area kuliner.',
                'price_weekday' => '10000',
                'price_weekend' => '15000',
                'open_time' => '08:00',
                'close_time' => '20:00',
                'location' => 'Banyumas',
                'image' => 'assets/img/bg.png',
                'instagram' => 'https://instagram.com/bludpariwisata',
                'tiktok' => null,
            ],
            [
                'name' => 'Teratai Mas',
                'slug' => 'teratai-mas',
                'description' => 'Destinasi wisata dan ruang publik yang dikelola BLUD Pariwisata.',
                'price_weekday' => '10000',
                'price_weekend' => '15000',
                'open_time' => '08:00',
                'close_time' => '20:00',
                'location' => 'Banyumas',
                'image' => 'assets/img/teratai.jpg',
                'instagram' => 'https://instagram.com/bludpariwisata',
                'tiktok' => null,
            ],
        ];

        foreach ($contents as $contentData) {
            $content = Content::updateOrCreate(
                ['slug' => $contentData['slug']],
                $contentData
            );

            ContentFeature::updateOrCreate(
                ['location' => $content->id, 'type' => 'facility', 'facility_name' => 'Area parkir'],
                ['icon' => 'assets/svg/location.svg']
            );
        }

        News::updateOrCreate(
            ['title' => 'BLUD Pariwisata Banyumas Siap Melayani Pengunjung'],
            [
                'content' => 'Informasi wisata, jadwal kegiatan, dan layanan booking kini dapat diakses melalui website ini.',
                'upload_time' => Carbon::now(),
                'source' => url('/'),
                'image' => 'assets/img/logo blud.png',
            ]
        );

        Event::updateOrCreate(
            ['name_event' => 'Festival Wisata Banyumas', 'location' => 'Menara Teratai'],
            [
                'vendor' => 'BLUD Pariwisata',
                'start_date' => Carbon::now()->addDays(7)->toDateString(),
                'end_date' => Carbon::now()->addDays(7)->toDateString(),
                'file' => null,
            ]
        );
    }

    private function copyDemoImages(): void
    {
        $source = public_path('assets/img');
        $target = storage_path('app/public/assets/img');

        File::ensureDirectoryExists($target);

        foreach (['bg.png', 'logo blud.png', 'menara.jpg', 'teratai.jpg'] as $file) {
            if (File::exists($source.DIRECTORY_SEPARATOR.$file)) {
                File::copy($source.DIRECTORY_SEPARATOR.$file, $target.DIRECTORY_SEPARATOR.$file);
            }
        }
    }
}
