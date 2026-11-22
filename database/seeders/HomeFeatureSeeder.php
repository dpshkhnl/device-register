<?php

namespace Database\Seeders;

use App\Models\HomeFeature;
use Illuminate\Database\Seeder;

class HomeFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['icon' => 'shield', 'title' => 'Secure IMEI verification', 'description' => 'Instantly check whether a device is active, transferred, lost, or flagged — before you buy.', 'button_label' => 'Verify now', 'button_url' => '/verification'],
            ['icon' => 'transfer', 'title' => 'Ownership transfer', 'description' => 'Hand a phone over with OTP verification on both sides and a full audit trail.', 'button_label' => 'Transfer a device', 'button_url' => '/transfer'],
            ['icon' => 'alert', 'title' => 'Lost & stolen reports', 'description' => 'Report a missing device in minutes to stop it being sold or used by anyone else.', 'button_label' => 'Report a device', 'button_url' => '/lost-found'],
            ['icon' => 'certificate', 'title' => 'Trusted status records', 'description' => 'A tamper-proof record of each device that buyers, shops and authorities can rely on.', 'button_label' => 'Check a status', 'button_url' => '/verification'],
        ];

        foreach ($features as $index => $feature) {
            HomeFeature::updateOrCreate(
                ['title' => $feature['title']],
                $feature + ['sort_order' => $index + 1, 'is_active' => true]
            );
        }
    }
}
