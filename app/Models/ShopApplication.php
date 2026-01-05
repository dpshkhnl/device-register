<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopApplication extends Model
{
    protected $fillable = [
        'user_id',
        'shop_name',
        'address',
        'business_registration_path',
        'store_photo_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
