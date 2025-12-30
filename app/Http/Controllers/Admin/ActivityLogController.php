<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FooterLink;
use App\Models\SystemSetting;

class ActivityLogController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $logs = ActivityLog::with('actor')->latest('created_at')->paginate(25);

        return view('admin.activity-logs.index', compact('logs', 'settings', 'footerLinks'));
    }
}
