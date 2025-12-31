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
    ];
}
