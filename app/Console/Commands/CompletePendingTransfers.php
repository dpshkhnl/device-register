<?php

namespace App\Console\Commands;

use App\Models\TransferRequest;
use App\Services\DeviceTransferService;
use Illuminate\Console\Command;

/**
 * One-time cleanup: transfers created before OTP-verified transfers completed
 * immediately were left "pending" waiting for the new owner to accept.
 */
class CompletePendingTransfers extends Command
{
    protected $signature = 'transfers:complete-pending {--dry-run : List what would happen without changing anything}';

    protected $description = 'Complete pending device transfers whose new owner has an account';

    public function handle(DeviceTransferService $transfers): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $pending = TransferRequest::with(['device', 'fromUser', 'toUser'])
            ->where('status', 'pending')
            ->orderBy('id')
            ->get();

        if ($pending->isEmpty()) {
            $this->info('No pending transfers.');

            return self::SUCCESS;
        }

        $rows = [];
        $completed = 0;

        foreach ($pending as $transfer) {
            $device = $transfer->device;
            $toMobile = $transfers->normalizeMobile($transfer->to_mobile, $transfer->fromUser?->country_code);
            $toUser = $transfer->toUser ?? $transfers->findUserByMobile($toMobile, $transfer->to_mobile);

            $skip = match (true) {
                ! $device => 'device deleted',
                $device->current_owner_id !== $transfer->from_user_id => 'device no longer owned by sender',
                $device->status === 'lost' => 'device marked lost',
                ! $toUser => 'new owner not registered (completes on sign-up)',
                $toUser->id === $transfer->from_user_id => 'recipient is the sender',
                default => null,
            };

            if (! $skip && ! $dryRun) {
                $transfers->complete($transfer, $toUser);
                $completed++;
            } elseif (! $toUser && ! $dryRun && $transfer->to_mobile !== $toMobile) {
                // Older requests stored the number as typed; normalize it so sign-up can claim it.
                $transfer->update(['to_mobile' => $toMobile]);
            }

            $rows[] = [
                $transfer->id,
                $device?->imei ?? '-',
                $transfer->fromUser?->name ?? '-',
                $toUser?->name ?? $transfer->to_mobile,
                $skip ? 'skipped: '.$skip : ($dryRun ? 'would complete' : 'completed'),
            ];
        }

        $this->table(['Transfer', 'IMEI', 'From', 'To', 'Result'], $rows);
        $this->info($dryRun
            ? 'Dry run: nothing changed.'
            : "Completed {$completed} of {$pending->count()} pending transfers.");

        return self::SUCCESS;
    }
}
