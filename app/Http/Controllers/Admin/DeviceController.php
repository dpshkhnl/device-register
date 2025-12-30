<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\FooterLink;
use App\Models\SystemSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $devices = Device::with('currentOwner')->latest()->paginate(20);

        return view('admin.devices.index', compact('devices', 'settings', 'footerLinks'));
    }

    public function show(Device $device)
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $device->load('currentOwner')->loadCount(['transferRequests', 'lostReports', 'certificates']);

        return view('admin.devices.show', compact('device', 'settings', 'footerLinks'));
    }

    public function update(Request $request, Device $device, ActivityLogger $logger)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,transferred,lost,suspicious'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $old = ['status' => $device->status];
        $device->update(['status' => $data['status']]);

        $logger->log('device_status_updated', $device, $old, [
            'status' => $data['status'],
            'reason' => $data['reason'] ?? null,
        ], $request->user()->id);

        return redirect()->route('admin.devices.index')->with('status', 'Device status updated.');
    }
}
