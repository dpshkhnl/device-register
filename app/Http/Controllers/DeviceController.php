<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\ImeiCheck;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function create()
    {
        $deviceTypes = [
            ['value' => 'smartphone', 'label' => 'Smartphone'],
            ['value' => 'tablet', 'label' => 'Tablet'],
            ['value' => 'smartwatch', 'label' => 'Smartwatch'],
            ['value' => 'laptop', 'label' => 'Laptop'],
            ['value' => 'other', 'label' => 'Other'],
        ];

        $brands = [
            'Apple',
            'Samsung',
            'Google',
            'OnePlus',
            'Xiaomi',
            'Huawei',
            'Oppo',
            'Vivo',
            'Realme',
            'Motorola',
            'Nokia',
            'Sony',
            'LG',
            'Asus',
            'Lenovo',
            'Other',
        ];

        return view('pages.register-device', compact('deviceTypes', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'imei' => ['required', 'digits:15', 'unique:devices,imei'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'device_type' => ['required', 'string', 'max:50'],
            'purchase_type' => ['required', 'in:new,secondhand'],
            'purchase_date' => ['required', 'date'],
            'invoice' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $invoicePath = null;
        if ($request->hasFile('invoice')) {
            $invoicePath = $request->file('invoice')->store('invoices', 'public');
        }

        $device = Device::create([
            'current_owner_id' => $request->user()->id,
            'imei' => $validated['imei'],
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'device_type' => $validated['device_type'],
            'purchase_type' => $validated['purchase_type'],
            'purchase_date' => $validated['purchase_date'],
            'invoice_path' => $invoicePath,
            'status' => 'active',
        ]);

        return redirect()
            ->route('devices.show', $device)
            ->with('status', 'Device registered successfully.');
    }

    public function show(Device $device, Request $request)
    {
        if ($device->current_owner_id !== $request->user()->id) {
            abort(403);
        }

        return view('pages.device-show', compact('device'));
    }

    public function verification(Request $request)
    {
        $imei = $request->input('imei');
        $device = null;
        $result = null;

        if ($imei !== null && $imei !== '') {
            $request->validate([
                'imei' => ['required', 'digits:15'],
            ]);

            $device = Device::with('currentOwner')->where('imei', $imei)->first();
            $result = $device ? 'found' : 'not_found';

            ImeiCheck::create([
                'imei' => $imei,
                'checked_by_user_id' => $request->user()?->id,
                'channel' => 'portal',
                'result' => $result,
                'status_returned' => $device?->status,
            ]);
        }

        return view('pages.verification', compact('imei', 'device', 'result'));
    }
}
