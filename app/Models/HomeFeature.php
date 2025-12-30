<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeFeature extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon',
        'button_label',
        'button_url',
        'sort_order',
        'is_active',
    ];
}
