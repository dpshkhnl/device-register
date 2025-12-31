<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\TransferRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $devices = Device::where('current_owner_id', $request->user()->id)
            ->latest()
            ->get();

        $pendingTransfers = TransferRequest::with(['device', 'fromUser'])
            ->where('status', 'pending')
            ->where(function ($query) use ($user) {
                $query->where('to_user_id', $user->id)
                    ->orWhere('to_mobile', $user->mobile);
            })
            ->latest()
            ->get();

        $outgoingPendingTransfers = TransferRequest::with(['device', 'toUser'])
            ->where('status', 'pending')
            ->where('from_user_id', $user->id)
            ->latest()
            ->get();

        $recentTransfers = TransferRequest::with(['device', 'fromUser'])
            ->where(function ($query) use ($user) {
                $query->where('from_user_id', $user->id)
                    ->orWhere('to_user_id', $user->id)
                    ->orWhere('to_mobile', $user->mobile);
            })
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'registered' => $devices->count(),
            'active' => $devices->where('status', 'active')->count(),
            'pending_transfers' => $pendingTransfers->count() + $outgoingPendingTransfers->count(),
        ];

        return view('pages.dashboard', compact('devices', 'stats', 'pendingTransfers', 'outgoingPendingTransfers', 'recentTransfers'));
    }
}
