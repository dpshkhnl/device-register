<?php

namespace App\Http\Controllers;

use App\Http\Requests\SystemSettingRequest;
use App\Models\SystemSetting;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function edit()
    {
        $setting = SystemSetting::first() ?? SystemSetting::create([]);

        return view('admin.settings.edit', compact('setting'));
    }

    public function update(SystemSettingRequest $request)
    {
        $setting = SystemSetting::first() ?? SystemSetting::create([]);
        $setting->update($request->validated());

        return redirect()->route('admin.settings.edit')->with('status', 'Settings updated successfully.');
    }

    public function updateSecurity(Request $request)
    {
        $data = $request->validate([
            'auth_force_otp' => ['nullable', 'boolean'],
            'otp_resend_seconds' => ['required', 'integer', 'min:10', 'max:600'],
        ]);

        $data['auth_force_otp'] = (bool) ($data['auth_force_otp'] ?? false);

        $setting = SystemSetting::first() ?? SystemSetting::create([]);
        $setting->update($data);

        return redirect()->route('admin.security.index')->with('status', 'Security settings updated.');
    }

    public function testEmail(Request $request, NotificationService $notifier)
    {
        $data = $request->validate([
            'test_email' => ['required', 'email', 'max:255'],
        ]);

        $notifier->sendEmail($data['test_email'], 'SMTP test email', 'This is a test email from DRMS settings.');

        return redirect()->route('admin.settings.edit')->with('status', 'Test email sent.');
    }

    public function testSms(Request $request, NotificationService $notifier)
    {
        $data = $request->validate([
            'test_sms' => ['required', 'string', 'max:20'],
        ]);

        $notifier->sendSms($data['test_sms'], 'This is a test SMS from DRMS settings.');

        return redirect()->route('admin.settings.edit')->with('status', 'Test SMS sent.');
    }
}
