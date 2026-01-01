<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LostReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'device_id',
        'reporter_user_id',
        'type',
        'description',
        'contact_phone_1',
        'contact_phone_2',
        'incident_type',
        'incident_date',
        'incident_location',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'incident_date' => 'date',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
