<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'imei',
        'brand',
        'model',
        'device_type',
        'purchase_type',
        'purchase_date',
        'status',
        'current_owner_id',
        'registered_at',
        'seller_name',
        'invoice_path',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'registered_at' => 'datetime',
    ];

    public function currentOwner()
    {
        return $this->belongsTo(User::class, 'current_owner_id');
    }

    public function ownerships()
    {
        return $this->hasMany(DeviceOwnership::class);
    }

    public function transferRequests()
    {
        return $this->hasMany(TransferRequest::class);
    }

    public function lostReports()
    {
        return $this->hasMany(LostReport::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
}
