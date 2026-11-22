<?php

namespace Database\Seeders;

use App\Models\HomeBanner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Homepage hero banners. Images ship in public/images/banners (Unsplash licence)
 * and are copied to the public disk so the admin banner screens can manage them like uploads.
 */
class HomeBannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Know the phone before you trust it',
                'subtitle' => 'Check any IMEI in seconds to see if a device is registered, active, or reported stolen.',
                'link_label' => 'Check an IMEI',
                'link_url' => '/verification',
                'image' => 'verify-before-you-buy.jpg',
            ],
            [
                'title' => 'Buying second-hand? Check it first',
                'subtitle' => 'See a device’s registry status before you hand over any money — and avoid buying stolen goods.',
                'link_label' => 'Run a free check',
                'link_url' => '/verification',
                'image' => 'second-hand-phones.jpg',
            ],
            [
                'title' => 'Selling? Transfer ownership in minutes',
                'subtitle' => 'Hand your phone over the right way. Both owners confirm with an SMS code and the record updates instantly.',
                'link_label' => 'Transfer a device',
                'link_url' => '/transfer',
                'image' => 'ownership-transfer.jpg',
            ],
            [
                'title' => 'Lost your phone? Flag it instantly',
                'subtitle' => 'Report it lost or stolen and anyone who checks its IMEI will be warned not to buy it.',
                'link_label' => 'Report a device',
                'link_url' => '/lost-found',
                'image' => 'lost-phone.jpg',
            ],
        ];

        foreach ($banners as $index => $banner) {
            $path = 'banners/seed-'.$banner['image'];
            $source = public_path('images/banners/'.$banner['image']);
            if (is_file($source) && ! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, file_get_contents($source));
            }

            HomeBanner::updateOrCreate(
                ['title' => $banner['title']],
                [
                    'subtitle' => $banner['subtitle'],
                    'link_label' => $banner['link_label'],
                    'link_url' => $banner['link_url'],
                    'image_url' => $path,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
