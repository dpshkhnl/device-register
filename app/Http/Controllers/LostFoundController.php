<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\LostReport;
use Illuminate\Http\Request;

class LostFoundController extends Controller
{
    public function index(Request $request)
    {
        $devices = Device::where('current_owner_id', $request->user()->id)
            ->orderBy('brand')
            ->orderBy('model')
            ->get();

        $reports = LostReport::with('device')
            ->where('reporter_user_id', $request->user()->id)
            ->latest()
            ->get()
            ->groupBy('device_id');

        return view('pages.lost-found', compact('devices', 'reports'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'device_id' => ['required', 'exists:devices,id'],
            'action' => ['required', 'in:lost,found'],
            'description' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'current_password'],
        ]);

        $device = Device::where('id', $data['device_id'])
            ->where('current_owner_id', $request->user()->id)
            ->firstOrFail();

        if ($data['action'] === 'lost' && $device->status === 'lost') {
            return back()->withErrors(['device_id' => 'This device is already marked as lost.']);
        }

        if ($data['action'] === 'found' && $device->status !== 'lost') {
            return back()->withErrors(['device_id' => 'Only lost devices can be marked as found.']);
        }

        LostReport::create([
            'device_id' => $device->id,
            'reporter_user_id' => $request->user()->id,
            'type' => $data['action'],
            'description' => $data['description'] ?? null,
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        if ($data['action'] === 'lost') {
            $device->update(['status' => 'lost']);
        } else {
            $device->update(['status' => 'active']);
        }

        return back()->with('status', 'Report submitted successfully.');
    }
}
