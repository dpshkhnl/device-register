<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'link_label',
        'link_url',
        'image_url',
        'sort_order',
        'is_active',
    ];
}
