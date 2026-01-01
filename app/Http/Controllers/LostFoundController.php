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
            'contact_phone_1' => ['required_if:action,lost', 'nullable', 'string', 'max:20'],
            'contact_phone_2' => ['nullable', 'string', 'max:20'],
            'incident_type' => ['required_if:action,lost', 'nullable', 'in:lost,stolen'],
            'incident_date' => ['required_if:action,lost', 'nullable', 'date'],
            'incident_location' => ['required_if:action,lost', 'nullable', 'string', 'max:255'],
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
            'contact_phone_1' => $data['contact_phone_1'] ?? null,
            'contact_phone_2' => $data['contact_phone_2'] ?? null,
            'incident_type' => $data['incident_type'] ?? null,
            'incident_date' => $data['incident_date'] ?? null,
            'incident_location' => $data['incident_location'] ?? null,
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
