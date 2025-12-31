<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'device_limit',
        'imei_limit',
        'duration_days',
        'is_trial',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_trial' => 'bool',
        'is_active' => 'bool',
    ];

    public function userPackages()
    {
        return $this->hasMany(UserPackage::class);
    }
}
