<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SystemSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'max:120'],
            'brand_primary' => ['required', 'string', 'max:20'],
            'brand_secondary' => ['required', 'string', 'max:20'],
            'hero_title' => ['required', 'string', 'max:200'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'hero_primary_label' => ['required', 'string', 'max:60'],
            'hero_primary_url' => ['required', 'string', 'max:255'],
            'hero_secondary_label' => ['required', 'string', 'max:60'],
            'hero_secondary_url' => ['required', 'string', 'max:255'],
            'cta_title' => ['required', 'string', 'max:200'],
            'cta_subtitle' => ['nullable', 'string', 'max:500'],
            'cta_primary_label' => ['required', 'string', 'max:60'],
            'cta_primary_url' => ['required', 'string', 'max:255'],
            'cta_secondary_label' => ['required', 'string', 'max:60'],
            'cta_secondary_url' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'sms_provider' => ['nullable', 'string', 'max:40'],
            'sms_api_url' => ['nullable', 'string', 'max:255'],
            'sms_token' => ['nullable', 'string', 'max:255'],
            'sms_sender' => ['nullable', 'string', 'max:60'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'string', 'max:20'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'banner_text' => ['nullable', 'string', 'max:160'],
            'banner_link_label' => ['nullable', 'string', 'max:60'],
            'banner_link_url' => ['nullable', 'string', 'max:255'],
        ];
    }
}
