<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Otp;
use App\Models\SystemSetting;
use App\Models\TransferRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TransferController extends Controller
{
    public function create(Request $request)
    {
        $devices = Device::where('current_owner_id', $request->user()->id)
            ->orderBy('brand')
            ->orderBy('model')
            ->get();

        $pendingDeviceIds = TransferRequest::where('status', 'pending')
            ->whereIn('device_id', $devices->pluck('id'))
            ->pluck('device_id')
            ->all();

        return view('pages.transfer', compact('devices', 'pendingDeviceIds'));
    }

    public function verifyNewOtp(Request $request)
    {
        $data = $request->validate([
            'device_imei' => ['required', 'digits:15'],
            'to_mobile' => ['required', 'string', 'max:20'],
            'new_owner_otp' => ['required', 'digits:6'],
        ]);

        $device = Device::where('imei', $data['device_imei'])
            ->where('current_owner_id', $request->user()->id)
            ->firstOrFail();

        if (TransferRequest::where('device_id', $device->id)->where('status', 'pending')->exists()) {
            return back()->withInput()->withErrors(['device_imei' => 'A pending transfer already exists for this device.']);
        }

        $otp = Otp::where('mobile', $data['to_mobile'])
            ->where('purpose', 'transfer')
            ->first();

        if (! $otp || $otp->expires_at?->isPast()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'OTP expired or not found. Please resend OTP.',
                    'errors' => ['new_owner_otp' => ['OTP expired or not found. Please resend OTP.']],
                ], 422);
            }
            return back()->withInput()->withErrors(['new_owner_otp' => 'OTP expired or not found. Please resend OTP.']);
        }

        if (! Hash::check($data['new_owner_otp'], $otp->otp_hash)) {
            $otp->increment('attempts');
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Invalid OTP. Please try again.',
                    'errors' => ['new_owner_otp' => ['Invalid OTP. Please try again.']],
                ], 422);
            }
            return back()->withInput()->withErrors(['new_owner_otp' => 'Invalid OTP. Please try again.']);
        }

        $request->session()->put('transfer.new_verified', true);
        $request->session()->put('transfer.device', $device->imei);
        $request->session()->forget('transfer.old_verified');

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'New owner OTP verified.',
            ]);
        }

        return back()->withInput()->with('status', 'New owner OTP verified.');
    }

    public function verifyOldOtp(Request $request)
    {
        if (! $request->session()->get('transfer.new_verified')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Please verify the new owner OTP first.',
                    'errors' => ['old_owner_otp' => ['Please verify the new owner OTP first.']],
                ], 422);
            }
            return back()->withInput()->withErrors(['old_owner_otp' => 'Please verify the new owner OTP first.']);
        }

        $data = $request->validate([
            'device_imei' => ['required', 'digits:15'],
            'old_owner_otp' => ['required', 'digits:6'],
        ]);

        $device = Device::where('imei', $data['device_imei'])
            ->where('current_owner_id', $request->user()->id)
            ->firstOrFail();

        if (TransferRequest::where('device_id', $device->id)->where('status', 'pending')->exists()) {
            return back()->withInput()->withErrors(['device_imei' => 'A pending transfer already exists for this device.']);
        }

        $oldOwnerMobile = $request->user()->mobile;
        if (! $oldOwnerMobile) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Please add your mobile number before requesting OTP.',
                    'errors' => ['old_owner_otp' => ['Please add your mobile number before requesting OTP.']],
                ], 422);
            }
            return back()->withInput()->withErrors(['old_owner_otp' => 'Please add your mobile number before requesting OTP.']);
        }

        $otp = Otp::where('mobile', $oldOwnerMobile)
            ->where('purpose', 'transfer')
            ->first();

        if (! $otp || $otp->expires_at?->isPast()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'OTP expired or not found. Please resend OTP.',
                    'errors' => ['old_owner_otp' => ['OTP expired or not found. Please resend OTP.']],
                ], 422);
            }
            return back()->withInput()->withErrors(['old_owner_otp' => 'OTP expired or not found. Please resend OTP.']);
        }

        if (! Hash::check($data['old_owner_otp'], $otp->otp_hash)) {
            $otp->increment('attempts');
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Invalid OTP. Please try again.',
                    'errors' => ['old_owner_otp' => ['Invalid OTP. Please try again.']],
                ], 422);
            }
            return back()->withInput()->withErrors(['old_owner_otp' => 'Invalid OTP. Please try again.']);
        }

        $request->session()->put('transfer.old_verified', true);
        $request->session()->put('transfer.device', $device->imei);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Old owner OTP verified.',
            ]);
        }

        return back()->withInput()->with('status', 'Old owner OTP verified.');
    }

    public function sendOtp(Request $request)
    {
        $recipient = $request->input('recipient', 'new');
        if (! in_array($recipient, ['new', 'old'], true)) {
            $recipient = 'new';
        }

        $rules = [
            'device_imei' => ['required', 'digits:15'],
        ];

        if ($recipient === 'new') {
            $rules['to_mobile'] = ['required', 'string', 'max:20'];
        }

        $data = $request->validate($rules);

        $device = Device::where('imei', $data['device_imei'])
            ->where('current_owner_id', $request->user()->id)
            ->firstOrFail();

        if (TransferRequest::where('device_id', $device->id)->where('status', 'pending')->exists()) {
            return back()->withInput()->withErrors(['device_imei' => 'A pending transfer already exists for this device.']);
        }

        $targetMobile = $recipient === 'old'
            ? $request->user()->mobile
            : $data['to_mobile'];

        if (! $targetMobile) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Please add your mobile number before requesting OTP.',
                    'errors' => ['old_owner_otp' => ['Please add your mobile number before requesting OTP.']],
                ], 422);
            }
            return back()->withInput()->withErrors(['old_owner_otp' => 'Please add your mobile number before requesting OTP.']);
        }

        $settings = SystemSetting::first();
        $resendSeconds = (int) ($settings?->otp_resend_seconds ?? 60);
        if ($resendSeconds < 10) {
            $resendSeconds = 10;
        } elseif ($resendSeconds > 600) {
            $resendSeconds = 600;
        }
        $existingOtp = Otp::where('mobile', $targetMobile)
            ->where('purpose', 'transfer')
            ->first();

        if ($resendSeconds > 0 && $existingOtp?->last_sent_at) {
            $elapsed = (int) now()->diffInSeconds($existingOtp->last_sent_at);
            if ($elapsed < 0) {
                $elapsed = 0;
            }
            if ($elapsed < $resendSeconds) {
                $remaining = (int) max(0, $resendSeconds - $elapsed);
                if ($request->expectsJson()) {
                    return response()->json([
                        'ok' => false,
                        'recipient' => $recipient,
                        'cooldown' => $remaining,
                        'resend_seconds' => $resendSeconds,
                    ], 429);
                }
                return back()
                    ->withInput()
                    ->with('otp_sent_recipient', $recipient)
                    ->with('otp_resend_seconds', $resendSeconds)
                    ->with('otp_cooldown', $remaining);
            }
        }

        $otpCode = (string) random_int(100000, 999999);

        Otp::updateOrCreate(
            [
                'mobile' => $targetMobile,
                'purpose' => 'transfer',
            ],
            [
                'otp_hash' => Hash::make($otpCode),
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0,
                'last_sent_at' => now(),
            ]
        );

        $response = back()
            ->withInput()
            ->with('status', $recipient === 'old' ? 'OTP sent to your number.' : 'OTP sent to '.$targetMobile.'.')
            ->with('otp_hint', $otpCode)
            ->with('otp_device', $device->imei)
            ->with('otp_new_sent', $recipient === 'new')
            ->with('otp_old_sent', $recipient === 'old')
            ->with('otp_sent_recipient', $recipient)
            ->with('otp_resend_seconds', $resendSeconds);

        if ($recipient === 'new') {
            $request->session()->forget(['transfer.new_verified', 'transfer.old_verified']);
        } else {
            $request->session()->forget('transfer.old_verified');
        }

        if ($recipient === 'new') {
            $response->with('dev_otp_new', $otpCode);
        } else {
            $response->with('dev_otp_old', $otpCode);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'recipient' => $recipient,
                'status' => $recipient === 'old' ? 'OTP sent to your number.' : 'OTP sent to '.$targetMobile.'.',
                'dev_otp' => $otpCode,
                'resend_seconds' => $resendSeconds,
            ]);
        }

        return $response;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'device_imei' => ['required', 'digits:15'],
            'to_mobile' => ['required', 'string', 'max:20'],
            'new_owner_otp' => ['required', 'digits:6'],
            'old_owner_otp' => ['required', 'digits:6'],
        ]);

        $device = Device::where('imei', $data['device_imei'])
            ->where('current_owner_id', $request->user()->id)
            ->firstOrFail();

        if (TransferRequest::where('device_id', $device->id)->where('status', 'pending')->exists()) {
            return back()->withInput()->withErrors(['device_imei' => 'A pending transfer already exists for this device.']);
        }

        $newOtp = Otp::where('mobile', $data['to_mobile'])
            ->where('purpose', 'transfer')
            ->first();

        if (! $newOtp || $newOtp->expires_at?->isPast()) {
            return back()->withInput()->withErrors(['new_owner_otp' => 'New owner OTP expired or not found. Please resend OTP.']);
        }

        if (! Hash::check($data['new_owner_otp'], $newOtp->otp_hash)) {
            $newOtp->increment('attempts');
            return back()->withInput()->withErrors(['new_owner_otp' => 'Invalid new owner OTP. Please try again.']);
        }

        $oldOwnerMobile = $request->user()->mobile;
        if (! $oldOwnerMobile) {
            return back()->withInput()->withErrors(['old_owner_otp' => 'Please add your mobile number before confirming transfer.']);
        }
        $oldOtp = Otp::where('mobile', $oldOwnerMobile)
            ->where('purpose', 'transfer')
            ->first();

        if (! $oldOtp || $oldOtp->expires_at?->isPast()) {
            return back()->withInput()->withErrors(['old_owner_otp' => 'Old owner OTP expired or not found. Please resend OTP.']);
        }

        if (! Hash::check($data['old_owner_otp'], $oldOtp->otp_hash)) {
            $oldOtp->increment('attempts');
            return back()->withInput()->withErrors(['old_owner_otp' => 'Invalid old owner OTP. Please try again.']);
        }

        $newOtp->delete();
        $oldOtp->delete();

        $toUser = User::where('mobile', $data['to_mobile'])->first();

        TransferRequest::create([
            'device_id' => $device->id,
            'from_user_id' => $request->user()->id,
            'to_mobile' => $data['to_mobile'],
            'to_user_id' => $toUser?->id,
            'status' => 'pending',
            'comment' => 'Transfer requested via portal',
            'confirmed_at' => Carbon::now(),
        ]);

        $request->session()->forget([
            'transfer.new_verified',
            'transfer.old_verified',
            'transfer.device',
            'transfer.dev_otp_new',
            'transfer.dev_otp_old',
        ]);

        return redirect()->route('dashboard')->with('status', 'Transfer request submitted.')->with('transfer_confirmed', true);
    }

    public function accept(Request $request, TransferRequest $transfer)
    {
        $user = $request->user();

        if ($transfer->status !== 'pending') {
            return back()->withErrors(['transfer' => 'Transfer is already processed.']);
        }

        if ($transfer->to_user_id && $transfer->to_user_id !== $user->id) {
            return back()->withErrors(['transfer' => 'You are not authorized to accept this transfer.']);
        }

        if (! $transfer->to_user_id && $transfer->to_mobile !== $user->mobile) {
            return back()->withErrors(['transfer' => 'You are not authorized to accept this transfer.']);
        }

        $transfer->update([
            'status' => 'accepted',
            'to_user_id' => $transfer->to_user_id ?? $user->id,
            'confirmed_at' => now(),
        ]);

        if ($transfer->device) {
            $transfer->device->update([
                'current_owner_id' => $user->id,
                'status' => 'transferred',
            ]);
        }

        return back()->with('status', 'Transfer accepted. Device ownership updated.');
    }

    public function cancel(Request $request, TransferRequest $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->withErrors(['transfer' => 'Only pending transfers can be cancelled.']);
        }

        if ($transfer->from_user_id !== $request->user()->id) {
            return back()->withErrors(['transfer' => 'You are not authorized to cancel this transfer.']);
        }

        $transfer->update([
            'status' => 'cancelled',
            'confirmed_at' => now(),
        ]);

        return back()->with('status', 'Transfer cancelled.');
    }
}
