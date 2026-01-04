<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'app_name',
        'brand_primary',
        'brand_secondary',
        'hero_title',
        'hero_subtitle',
        'hero_primary_label',
        'hero_primary_url',
        'hero_secondary_label',
        'hero_secondary_url',
        'cta_title',
        'cta_subtitle',
        'cta_primary_label',
        'cta_primary_url',
        'cta_secondary_label',
        'cta_secondary_url',
        'contact_email',
        'contact_phone',
        'sms_provider',
        'sms_api_url',
        'sms_token',
        'sms_sender',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'banner_text',
        'banner_link_label',
        'banner_link_url',
        'otp_resend_seconds',
        'auth_force_otp',
    ];
}
