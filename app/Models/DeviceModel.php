<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceModel extends Model
{
    protected $fillable = [
        'product_id',
        'brand_id',
        'name',
        'storage_options',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'storage_options' => 'array',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
