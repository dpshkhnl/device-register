<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceOwnership extends Model
{
    protected $fillable = [
        'device_id',
        'owner_id',
        'from_at',
        'to_at',
        'transfer_reason',
    ];

    protected $casts = [
        'from_at' => 'datetime',
        'to_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
