<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImeiCheck extends Model
{
    protected $fillable = [
        'imei',
        'checked_by_user_id',
        'channel',
        'result',
        'status_returned',
    ];

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by_user_id');
    }
}
