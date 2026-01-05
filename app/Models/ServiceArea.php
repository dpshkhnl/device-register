<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceArea extends Model
{
    protected $fillable = [
        'name',
        'iso2',
        'dial_code',
        'sort_order',
        'is_active',
        'allow_email_login',
        'require_email_otp',
        'allow_phone_login',
        'require_phone_otp',
    ];
}
