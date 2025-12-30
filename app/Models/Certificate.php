<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'device_id',
        'certificate_no',
        'issued_to_user_id',
        'status_at_issue',
        'pdf_path',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function issuedTo()
    {
        return $this->belongsTo(User::class, 'issued_to_user_id');
    }
}
