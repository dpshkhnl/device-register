<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function deviceModels()
    {
        return $this->hasMany(DeviceModel::class);
    }

    /**
     * Active products → brands → models → storage, shaped for the cascading
     * dropdowns on the device registration form.
     */
    public static function catalog(): array
    {
        $models = DeviceModel::query()
            ->join('brands', 'brands.id', '=', 'device_models.brand_id')
            ->where('device_models.is_active', true)
            ->where('brands.is_active', true)
            ->orderBy('brands.sort_order')
            ->orderBy('brands.name')
            ->orderBy('device_models.sort_order')
            ->orderBy('device_models.name')
            ->get(['device_models.*', 'brands.name as brand_name'])
            ->groupBy('product_id');

        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $product) => [
                'slug' => $product->slug,
                'name' => $product->name,
                'brands' => ($models[$product->id] ?? collect())
                    ->groupBy('brand_name')
                    ->map(fn ($brandModels, $brandName) => [
                        'name' => $brandName,
                        'models' => $brandModels->map(fn ($model) => [
                            'name' => $model->name,
                            'storage' => $model->storage_options ?? [],
                        ])->values(),
                    ])
                    ->values(),
            ])
            ->filter(fn ($product) => $product['brands']->isNotEmpty())
            ->values()
            ->toArray();
    }
}
