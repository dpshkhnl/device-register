<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $devices = Device::where('current_owner_id', $request->user()->id)
            ->latest()
            ->get();

        $stats = [
            'registered' => $devices->count(),
            'active' => $devices->where('status', 'active')->count(),
            'pending_transfers' => 0,
        ];

        return view('pages.dashboard', compact('devices', 'stats'));
    }
}
