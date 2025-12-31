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
        $userPackage = $request->user()->activePackage()->first();
        if (! $userPackage) {
            return back()->withErrors(['imei' => 'You need an active package to register devices.'])->withInput();
        }

        if ($userPackage->remainingDevices() <= 0) {
            return back()->withErrors(['imei' => 'Your device limit has been reached. Please upgrade your package.'])->withInput();
        }

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

        $userPackage->increment('used_device_count');

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

            if ($request->user()) {
                $userPackage = $request->user()->activePackage()->first();
                if (! $userPackage) {
                    return back()->withErrors(['imei' => 'You need an active package to verify IMEI.'])->withInput();
                }

                if ($userPackage->remainingImeiChecks() <= 0) {
                    return back()->withErrors(['imei' => 'Your IMEI verification limit has been reached.'])->withInput();
                }
            }

            $device = Device::with('currentOwner')->where('imei', $imei)->first();
            $result = $device ? 'found' : 'not_found';

            ImeiCheck::create([
                'imei' => $imei,
                'checked_by_user_id' => $request->user()?->id,
                'channel' => 'portal',
                'result' => $result,
                'status_returned' => $device?->status,
            ]);

            if ($request->user()) {
                $request->user()->activePackage()->first()?->increment('used_imei_count');
            }
        }

        return view('pages.verification', compact('imei', 'device', 'result'));
    }
}
