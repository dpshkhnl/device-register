<?php

namespace App\Services;

use App\Models\DeviceOwnership;
use App\Models\TransferRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeviceTransferService
{
    public function __construct(protected NotificationService $notifier)
    {
    }

    /**
     * Normalize a typed mobile to the stored +<dial code><number> format,
     * using the sender's country code when the input has none.
     */
    public function normalizeMobile(string $mobile, ?string $countryCode): string
    {
        $mobile = preg_replace('/[\s\-()]/', '', $mobile);
        if (str_starts_with($mobile, '+') || ! $countryCode) {
            return $mobile;
        }

        return $countryCode.ltrim($mobile, '0');
    }

    public function findUserByMobile(string $mobile, ?string $raw = null): ?User
    {
        return User::whereIn('mobile', array_filter([$mobile, $raw]))->first();
    }

    /**
     * Move the device to the new owner, close the old ownership record and notify both parties.
     */
    public function complete(TransferRequest $transfer, User $toUser): void
    {
        DB::transaction(function () use ($transfer, $toUser) {
            $device = $transfer->device()->lockForUpdate()->firstOrFail();
            $now = now();

            DeviceOwnership::where('device_id', $device->id)->whereNull('to_at')->update(['to_at' => $now]);
            if (! DeviceOwnership::where('device_id', $device->id)->exists()) {
                // Devices registered before ownership history existed: record the original owner too.
                DeviceOwnership::create([
                    'device_id' => $device->id,
                    'owner_id' => $device->current_owner_id,
                    'from_at' => $device->created_at,
                    'to_at' => $now,
                ]);
            }
            DeviceOwnership::create([
                'device_id' => $device->id,
                'owner_id' => $toUser->id,
                'from_at' => $now,
                'transfer_reason' => 'Transfer #'.$transfer->id,
            ]);

            $device->update([
                'current_owner_id' => $toUser->id,
                'status' => 'transferred',
            ]);

            $transfer->update([
                'status' => 'accepted',
                'to_user_id' => $toUser->id,
                'confirmed_at' => $now,
            ]);
        });

        $transfer->load(['device', 'fromUser']);
        $device = $transfer->device;
        $fromUser = $transfer->fromUser;
        $deviceLabel = trim($device->brand.' '.$device->model);

        $messageOld = "Device {$deviceLabel} (IMEI {$device->imei}) ownership transferred to {$toUser->name}.";
        $messageNew = "You are now the owner of {$deviceLabel} (IMEI {$device->imei}).";

        $this->notifier->sendEmail($fromUser?->email, 'Device ownership transferred', $messageOld);
        $this->notifier->sendSms($fromUser?->mobile, $messageOld);
        $this->notifier->sendEmail($toUser->email, 'Device ownership transferred', $messageNew);
        $this->notifier->sendSms($toUser->mobile, $messageNew);
    }

    /**
     * Complete transfers that were waiting for this mobile number to register.
     */
    public function claimPendingFor(User $user): void
    {
        if (! $user->mobile) {
            return;
        }

        TransferRequest::where('status', 'pending')
            ->whereNull('to_user_id')
            ->where('to_mobile', $user->mobile)
            ->get()
            ->each(fn (TransferRequest $transfer) => $this->complete($transfer, $user));
    }
}
