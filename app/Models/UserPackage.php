<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPackage extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'device_limit',
        'imei_limit',
        'used_device_count',
        'used_imei_count',
        'starts_at',
        'ends_at',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function remainingDevices(): int
    {
        return max(0, (int) $this->device_limit - (int) $this->used_device_count);
    }

    public function remainingImeiChecks(): int
    {
        return max(0, (int) $this->imei_limit - (int) $this->used_imei_count);
    }
}
