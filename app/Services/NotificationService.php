<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendSms(?string $to, string $message): void
    {
        $to = trim((string) $to);
        if ($to === '') {
            return;
        }

        $settings = SystemSetting::first();
        if (! $settings?->sms_api_url || ! $settings?->sms_token) {
            return;
        }

        $payload = [
            'auth_token' => $settings->sms_token,
            'to' => $to,
            'text' => $message,
        ];

        if ($settings->sms_sender) {
            $payload['from'] = $settings->sms_sender;
        }

        try {
            Http::asForm()->timeout(10)->post($settings->sms_api_url, $payload);
        } catch (\Throwable $e) {
            Log::warning('sms_send_failed', [
                'error' => $e->getMessage(),
                'to' => $to,
            ]);
        }
    }

    public function sendEmail(?string $to, string $subject, string $message): void
    {
        $to = trim((string) $to);
        if ($to === '') {
            return;
        }

        $settings = SystemSetting::first();
        if ($settings) {
            $mailConfig = [];
            if ($settings->mail_host) {
                $mailConfig['mail.mailers.smtp.host'] = $settings->mail_host;
            }
            if ($settings->mail_port) {
                $mailConfig['mail.mailers.smtp.port'] = (int) $settings->mail_port;
            }
            if ($settings->mail_username) {
                $mailConfig['mail.mailers.smtp.username'] = $settings->mail_username;
            }
            if ($settings->mail_password) {
                $mailConfig['mail.mailers.smtp.password'] = $settings->mail_password;
            }
            if ($settings->mail_encryption) {
                $mailConfig['mail.mailers.smtp.encryption'] = $settings->mail_encryption;
            }
            if ($settings->mail_from_address) {
                $mailConfig['mail.from.address'] = $settings->mail_from_address;
            }
            if ($settings->mail_from_name) {
                $mailConfig['mail.from.name'] = $settings->mail_from_name;
            }
            if ($mailConfig) {
                config($mailConfig);
            }
        }

        try {
            Mail::raw($message, function ($mail) use ($to, $subject, $settings) {
                $mail->to($to)->subject($subject);

                if ($settings?->contact_email) {
                    $mail->replyTo($settings->contact_email);
                }
            });
        } catch (\Throwable $e) {
            Log::warning('email_send_failed', [
                'error' => $e->getMessage(),
                'to' => $to,
            ]);
        }
    }
}
