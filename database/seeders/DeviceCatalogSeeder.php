<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\DeviceModel;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Seeds products, brands and models for the device registration dropdowns.
 * Safe to re-run: existing rows are updated, nothing is deleted.
 * To add a model, append it under its product + brand below and run:
 *   php artisan db:seed --class=DeviceCatalogSeeder
 * (or manage it from Admin → Device Models).
 */
class DeviceCatalogSeeder extends Seeder
{
    protected array $products = [
        'smartphone' => 'Mobile Phone',
        'tablet' => 'Tablet',
        'smartwatch' => 'Smartwatch',
        'laptop' => 'Laptop',
        'other' => 'Other',
    ];

    // product slug => brand => model => storage options
    protected array $catalog = [
        'smartphone' => [
            'Apple' => [
                'iPhone 17 Pro Max' => ['256GB', '512GB', '1TB', '2TB'],
                'iPhone 17 Pro' => ['256GB', '512GB', '1TB'],
                'iPhone Air' => ['256GB', '512GB', '1TB'],
                'iPhone 17' => ['256GB', '512GB'],
                'iPhone 16e' => ['128GB', '256GB', '512GB'],
                'iPhone 16 Pro Max' => ['256GB', '512GB', '1TB'],
                'iPhone 16 Pro' => ['128GB', '256GB', '512GB', '1TB'],
                'iPhone 16 Plus' => ['128GB', '256GB', '512GB'],
                'iPhone 16' => ['128GB', '256GB', '512GB'],
                'iPhone 15 Pro Max' => ['256GB', '512GB', '1TB'],
                'iPhone 15 Pro' => ['128GB', '256GB', '512GB', '1TB'],
                'iPhone 15 Plus' => ['128GB', '256GB', '512GB'],
                'iPhone 15' => ['128GB', '256GB', '512GB'],
                'iPhone 14 Pro Max' => ['128GB', '256GB', '512GB', '1TB'],
                'iPhone 14 Pro' => ['128GB', '256GB', '512GB', '1TB'],
                'iPhone 14 Plus' => ['128GB', '256GB', '512GB'],
                'iPhone 14' => ['128GB', '256GB', '512GB'],
                'iPhone 13 Pro Max' => ['128GB', '256GB', '512GB', '1TB'],
                'iPhone 13 Pro' => ['128GB', '256GB', '512GB', '1TB'],
                'iPhone 13' => ['128GB', '256GB', '512GB'],
                'iPhone 13 mini' => ['128GB', '256GB', '512GB'],
                'iPhone 12 Pro Max' => ['128GB', '256GB', '512GB'],
                'iPhone 12 Pro' => ['128GB', '256GB', '512GB'],
                'iPhone 12' => ['64GB', '128GB', '256GB'],
                'iPhone 11' => ['64GB', '128GB', '256GB'],
                'iPhone SE (3rd gen)' => ['64GB', '128GB', '256GB'],
            ],
            'Samsung' => [
                'Galaxy S25 Ultra' => ['256GB', '512GB', '1TB'],
                'Galaxy S25+' => ['256GB', '512GB'],
                'Galaxy S25' => ['128GB', '256GB', '512GB'],
                'Galaxy S25 Edge' => ['256GB', '512GB'],
                'Galaxy S24 Ultra' => ['256GB', '512GB', '1TB'],
                'Galaxy S24+' => ['256GB', '512GB'],
                'Galaxy S24' => ['128GB', '256GB', '512GB'],
                'Galaxy S24 FE' => ['128GB', '256GB', '512GB'],
                'Galaxy S23 Ultra' => ['256GB', '512GB', '1TB'],
                'Galaxy S23' => ['128GB', '256GB', '512GB'],
                'Galaxy Z Fold7' => ['256GB', '512GB', '1TB'],
                'Galaxy Z Flip7' => ['256GB', '512GB'],
                'Galaxy Z Fold6' => ['256GB', '512GB', '1TB'],
                'Galaxy Z Flip6' => ['256GB', '512GB'],
                'Galaxy A56 5G' => ['128GB', '256GB'],
                'Galaxy A55 5G' => ['128GB', '256GB'],
                'Galaxy A36 5G' => ['128GB', '256GB'],
                'Galaxy A35 5G' => ['128GB', '256GB'],
                'Galaxy A26 5G' => ['128GB', '256GB'],
                'Galaxy A16' => ['128GB', '256GB'],
                'Galaxy A06' => ['64GB', '128GB'],
                'Galaxy M35 5G' => ['128GB', '256GB'],
                'Galaxy F15 5G' => ['128GB'],
            ],
            'Google' => [
                'Pixel 10 Pro XL' => ['256GB', '512GB', '1TB'],
                'Pixel 10 Pro' => ['128GB', '256GB', '512GB', '1TB'],
                'Pixel 10' => ['128GB', '256GB'],
                'Pixel 9 Pro XL' => ['128GB', '256GB', '512GB', '1TB'],
                'Pixel 9 Pro' => ['128GB', '256GB', '512GB', '1TB'],
                'Pixel 9' => ['128GB', '256GB'],
                'Pixel 9a' => ['128GB', '256GB'],
                'Pixel 8 Pro' => ['128GB', '256GB', '512GB', '1TB'],
                'Pixel 8' => ['128GB', '256GB'],
                'Pixel 8a' => ['128GB', '256GB'],
            ],
            'OnePlus' => [
                'OnePlus 13' => ['256GB', '512GB'],
                'OnePlus 13R' => ['256GB', '512GB'],
                'OnePlus 12' => ['256GB', '512GB'],
                'OnePlus 12R' => ['128GB', '256GB'],
                'OnePlus Nord 5' => ['256GB', '512GB'],
                'OnePlus Nord 4' => ['128GB', '256GB', '512GB'],
                'OnePlus Nord CE4' => ['128GB', '256GB'],
                'OnePlus Nord CE4 Lite' => ['128GB', '256GB'],
            ],
            'Xiaomi' => [
                'Xiaomi 15 Ultra' => ['256GB', '512GB', '1TB'],
                'Xiaomi 15' => ['256GB', '512GB'],
                'Xiaomi 14T Pro' => ['256GB', '512GB', '1TB'],
                'Xiaomi 14T' => ['256GB', '512GB'],
                'Redmi Note 14 Pro+ 5G' => ['256GB', '512GB'],
                'Redmi Note 14 Pro 5G' => ['128GB', '256GB', '512GB'],
                'Redmi Note 14 5G' => ['128GB', '256GB'],
                'Redmi Note 14' => ['128GB', '256GB'],
                'Redmi Note 13 Pro+ 5G' => ['256GB', '512GB'],
                'Redmi Note 13 Pro' => ['128GB', '256GB', '512GB'],
                'Redmi Note 13' => ['128GB', '256GB'],
                'Redmi 14C' => ['64GB', '128GB', '256GB'],
                'Redmi 13C' => ['64GB', '128GB', '256GB'],
                'Redmi A3' => ['64GB', '128GB'],
                'POCO X7 Pro' => ['256GB', '512GB'],
                'POCO X6 Pro' => ['256GB', '512GB'],
                'POCO F7' => ['256GB', '512GB'],
                'POCO M6 Pro' => ['256GB', '512GB'],
            ],
            'Huawei' => [
                'Pura 70 Ultra' => ['512GB', '1TB'],
                'Pura 70 Pro' => ['512GB'],
                'Mate X6' => ['512GB'],
                'nova 13 Pro' => ['512GB'],
                'nova 12i' => ['128GB'],
            ],
            'Honor' => [
                'Honor Magic7 Pro' => ['512GB'],
                'Honor 400' => ['256GB', '512GB'],
                'Honor 200' => ['256GB', '512GB'],
                'Honor X9c' => ['256GB', '512GB'],
                'Honor X9b' => ['256GB'],
                'Honor X8c' => ['256GB'],
                'Honor X7c' => ['128GB', '256GB'],
            ],
            'Oppo' => [
                'Find X8 Pro' => ['512GB'],
                'Reno14 Pro 5G' => ['512GB'],
                'Reno14 5G' => ['256GB', '512GB'],
                'Reno13 Pro 5G' => ['256GB', '512GB'],
                'Reno13 5G' => ['256GB', '512GB'],
                'Reno12 Pro 5G' => ['256GB', '512GB'],
                'Reno12 F' => ['256GB'],
                'A5 Pro' => ['128GB', '256GB'],
                'A3x' => ['64GB', '128GB'],
                'A60' => ['128GB', '256GB'],
            ],
            'Vivo' => [
                'X200 Pro' => ['512GB'],
                'X200' => ['256GB', '512GB'],
                'V50' => ['256GB', '512GB'],
                'V40' => ['256GB', '512GB'],
                'V40e' => ['128GB', '256GB'],
                'Y39 5G' => ['128GB', '256GB'],
                'Y29' => ['128GB', '256GB'],
                'Y19s' => ['128GB'],
                'Y18' => ['64GB', '128GB'],
                'T3 Ultra' => ['256GB'],
            ],
            'Realme' => [
                'Realme GT 7 Pro' => ['256GB', '512GB'],
                'Realme 14 Pro+ 5G' => ['256GB', '512GB'],
                'Realme 14 Pro 5G' => ['256GB', '512GB'],
                'Realme 13 Pro+ 5G' => ['256GB', '512GB'],
                'Realme 12 Pro+ 5G' => ['256GB', '512GB'],
                'Realme C75' => ['128GB', '256GB'],
                'Realme C67' => ['128GB', '256GB'],
                'Realme C61' => ['64GB', '128GB'],
                'Realme Note 60' => ['64GB', '128GB'],
                'Realme Narzo 70 Pro 5G' => ['128GB', '256GB'],
            ],
            'Tecno' => [
                'Camon 40 Pro' => ['256GB'],
                'Camon 30' => ['256GB'],
                'Pova 7 Pro' => ['256GB'],
                'Pova 6 Pro' => ['256GB'],
                'Spark 40 Pro' => ['256GB'],
                'Spark 30' => ['128GB', '256GB'],
                'Spark 20' => ['128GB', '256GB'],
                'Spark Go 2025' => ['64GB', '128GB'],
            ],
            'Infinix' => [
                'Note 50 Pro' => ['256GB'],
                'Note 40 Pro' => ['256GB'],
                'Note 40' => ['256GB'],
                'Hot 50 Pro' => ['256GB'],
                'Hot 50' => ['128GB', '256GB'],
                'Hot 40i' => ['128GB', '256GB'],
                'Smart 9' => ['64GB', '128GB'],
                'Smart 8' => ['64GB', '128GB'],
            ],
            'Nothing' => [
                'Phone (3)' => ['256GB', '512GB'],
                'Phone (3a) Pro' => ['256GB'],
                'Phone (3a)' => ['128GB', '256GB'],
                'Phone (2a)' => ['128GB', '256GB'],
                'CMF Phone 2 Pro' => ['128GB', '256GB'],
                'CMF Phone 1' => ['128GB', '256GB'],
            ],
            'Motorola' => [
                'Edge 60 Pro' => ['256GB', '512GB'],
                'Edge 50 Pro' => ['256GB'],
                'Edge 50 Fusion' => ['128GB', '256GB'],
                'Razr 60 Ultra' => ['512GB'],
                'Moto G85 5G' => ['128GB', '256GB'],
                'Moto G64 5G' => ['128GB', '256GB'],
                'Moto G34 5G' => ['128GB'],
            ],
            'Nokia' => [
                'Nokia G42 5G' => ['128GB'],
                'Nokia C32' => ['64GB', '128GB'],
                'Nokia 105 (Feature phone)' => [],
                'Nokia 110 4G (Feature phone)' => [],
            ],
            'Sony' => [
                'Xperia 1 VI' => ['256GB', '512GB'],
                'Xperia 10 VI' => ['128GB'],
            ],
            'Asus' => [
                'ROG Phone 9 Pro' => ['512GB', '1TB'],
                'ROG Phone 9' => ['256GB', '512GB'],
                'Zenfone 11 Ultra' => ['256GB', '512GB'],
            ],
            'Lenovo' => [
                'Legion Y70' => ['256GB', '512GB'],
            ],
            'Other' => [
                'Other' => [],
            ],
        ],
        'tablet' => [
            'Apple' => [
                'iPad Pro 13" (M4)' => ['256GB', '512GB', '1TB', '2TB'],
                'iPad Pro 11" (M4)' => ['256GB', '512GB', '1TB', '2TB'],
                'iPad Air 13" (M3)' => ['128GB', '256GB', '512GB', '1TB'],
                'iPad Air 11" (M3)' => ['128GB', '256GB', '512GB', '1TB'],
                'iPad (A16)' => ['128GB', '256GB', '512GB'],
                'iPad (10th gen)' => ['64GB', '256GB'],
                'iPad mini (A17 Pro)' => ['128GB', '256GB', '512GB'],
            ],
            'Samsung' => [
                'Galaxy Tab S10 Ultra' => ['256GB', '512GB', '1TB'],
                'Galaxy Tab S10+' => ['256GB', '512GB'],
                'Galaxy Tab S10 FE' => ['128GB', '256GB'],
                'Galaxy Tab S9 FE' => ['128GB', '256GB'],
                'Galaxy Tab A9+' => ['64GB', '128GB'],
                'Galaxy Tab A9' => ['64GB', '128GB'],
            ],
            'Xiaomi' => [
                'Xiaomi Pad 7' => ['128GB', '256GB'],
                'Xiaomi Pad 6' => ['128GB', '256GB'],
                'Redmi Pad Pro' => ['128GB', '256GB'],
                'Redmi Pad SE' => ['128GB', '256GB'],
            ],
            'Lenovo' => [
                'Tab P12' => ['128GB', '256GB'],
                'Tab M11' => ['64GB', '128GB'],
            ],
            'Other' => [
                'Other' => [],
            ],
        ],
        'smartwatch' => [
            'Apple' => [
                'Apple Watch Ultra 3' => [],
                'Apple Watch Series 11' => [],
                'Apple Watch SE 3' => [],
                'Apple Watch Ultra 2' => [],
                'Apple Watch Series 10' => [],
                'Apple Watch SE (2nd gen)' => [],
            ],
            'Samsung' => [
                'Galaxy Watch8 Classic' => [],
                'Galaxy Watch8' => [],
                'Galaxy Watch Ultra' => [],
                'Galaxy Watch7' => [],
                'Galaxy Watch FE' => [],
            ],
            'Google' => [
                'Pixel Watch 4' => [],
                'Pixel Watch 3' => [],
            ],
            'Huawei' => [
                'Watch GT 5 Pro' => [],
                'Watch GT 5' => [],
                'Watch Fit 4' => [],
            ],
            'Xiaomi' => [
                'Redmi Watch 5' => [],
                'Xiaomi Watch S4' => [],
            ],
            'Other' => [
                'Other' => [],
            ],
        ],
        'laptop' => [
            'Apple' => [
                'MacBook Air 13" (M4)' => ['256GB', '512GB', '1TB', '2TB'],
                'MacBook Air 15" (M4)' => ['256GB', '512GB', '1TB', '2TB'],
                'MacBook Air 13" (M3)' => ['256GB', '512GB', '1TB', '2TB'],
                'MacBook Pro 14" (M4)' => ['512GB', '1TB', '2TB'],
                'MacBook Pro 16" (M4 Pro)' => ['512GB', '1TB', '2TB', '4TB'],
            ],
            'Dell' => [
                'XPS 13' => ['512GB', '1TB'],
                'XPS 14' => ['512GB', '1TB'],
                'Inspiron 15' => ['256GB', '512GB', '1TB'],
                'Vostro 15' => ['256GB', '512GB'],
                'Latitude 5440' => ['256GB', '512GB'],
            ],
            'HP' => [
                'Spectre x360 14' => ['512GB', '1TB'],
                'Envy x360 14' => ['512GB', '1TB'],
                'Pavilion 15' => ['512GB', '1TB'],
                'HP 15s' => ['256GB', '512GB'],
                'Victus 15' => ['512GB', '1TB'],
                'OMEN 16' => ['512GB', '1TB'],
            ],
            'Lenovo' => [
                'ThinkPad X1 Carbon' => ['512GB', '1TB'],
                'ThinkPad E14' => ['256GB', '512GB'],
                'IdeaPad Slim 3' => ['256GB', '512GB'],
                'IdeaPad Slim 5' => ['512GB', '1TB'],
                'Yoga Slim 7' => ['512GB', '1TB'],
                'Legion 5' => ['512GB', '1TB'],
                'LOQ 15' => ['512GB', '1TB'],
            ],
            'Asus' => [
                'Zenbook 14 OLED' => ['512GB', '1TB'],
                'Vivobook 15' => ['512GB'],
                'ROG Strix G16' => ['512GB', '1TB'],
                'TUF Gaming F15' => ['512GB', '1TB'],
            ],
            'Acer' => [
                'Swift Go 14' => ['512GB', '1TB'],
                'Aspire 7' => ['512GB'],
                'Aspire Lite 15' => ['512GB'],
                'Nitro V 15' => ['512GB', '1TB'],
            ],
            'MSI' => [
                'Modern 14' => ['512GB'],
                'Katana 15' => ['512GB', '1TB'],
                'Thin GF63' => ['512GB'],
            ],
            'Other' => [
                'Other' => [],
            ],
        ],
        'other' => [
            'Other' => [
                'Other' => [],
            ],
        ],
    ];

    public function run(): void
    {
        $productIds = [];
        foreach (array_keys($this->products) as $index => $slug) {
            $productIds[$slug] = Product::updateOrCreate(
                ['slug' => $slug],
                ['name' => $this->products[$slug], 'sort_order' => $index, 'is_active' => true],
            )->id;
        }

        $brandIds = Brand::pluck('id', 'name')->all();
        $nextBrandOrder = (int) Brand::max('sort_order') + 1;

        foreach ($this->catalog as $slug => $brands) {
            foreach ($brands as $brandName => $models) {
                if (! isset($brandIds[$brandName])) {
                    $brandIds[$brandName] = Brand::create([
                        'name' => $brandName,
                        'sort_order' => $brandName === 'Other' ? 999 : $nextBrandOrder++,
                        'is_active' => true,
                    ])->id;
                }

                $order = 0;
                foreach ($models as $modelName => $storage) {
                    DeviceModel::updateOrCreate(
                        [
                            'product_id' => $productIds[$slug],
                            'brand_id' => $brandIds[$brandName],
                            'name' => $modelName,
                        ],
                        [
                            'storage_options' => $storage ?: null,
                            'sort_order' => $order++,
                            'is_active' => true,
                        ],
                    );
                }
            }
        }
    }
}
