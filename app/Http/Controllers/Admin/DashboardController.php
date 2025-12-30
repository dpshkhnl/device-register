<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Device;
use App\Models\FooterLink;
use App\Models\LostReport;
use App\Models\SystemSetting;
use App\Models\TransferRequest;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $stats = [
            'users' => User::count(),
            'devices' => Device::count(),
            'active_devices' => Device::where('status', 'active')->count(),
            'pending_transfers' => TransferRequest::where('status', 'pending')->count(),
            'pending_reports' => LostReport::where('status', 'pending')->count(),
            'certificates' => Certificate::count(),
        ];

        return view('admin.dashboard', compact('settings', 'footerLinks', 'stats'));
    }
}
