<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceAgeOption;
use App\Models\DeviceModel;
use App\Models\ImeiCheck;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function create(Request $request)
    {
        $packageError = $this->packageError($request->user()->activePackage()->first());
        $catalog = Product::catalog();
        $deviceAges = DeviceAgeOption::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->pluck('label');

        return view('pages.register-device', compact('catalog', 'deviceAges', 'packageError'));
    }

    public function store(Request $request, NotificationService $notifier)
    {
        $userPackage = $request->user()->activePackage()->first();
        if ($packageError = $this->packageError($userPackage)) {
            return back()->withErrors(['package' => $packageError])->withInput();
        }

        $validated = $request->validate([
            'imei' => ['required', 'digits:15', 'unique:devices,imei'],
            'imei2' => ['nullable', 'digits:15', 'unique:devices,imei2', 'different:imei'],
            'product' => ['required', 'string', 'max:50'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:120'],
            'storage' => ['nullable', 'string', 'max:20'],
            'purchase_type' => ['required', 'in:new,secondhand'],
            'purchase_date' => ['required_if:purchase_type,new', 'nullable', 'date', 'before_or_equal:today'],
            'device_age' => [
                'required_if:purchase_type,secondhand', 'nullable', 'string',
                Rule::exists('device_age_options', 'label')->where('is_active', true),
            ],
            'invoice' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $deviceModel = DeviceModel::query()
            ->where('name', $validated['model'])
            ->where('is_active', true)
            ->whereHas('product', fn ($q) => $q->where('slug', $validated['product'])->where('is_active', true))
            ->whereHas('brand', fn ($q) => $q->where('name', $validated['brand'])->where('is_active', true))
            ->first();
        if (! $deviceModel) {
            return back()->withErrors(['model' => 'Please select a valid product, brand and model.'])->withInput();
        }

        $storageOptions = $deviceModel->storage_options ?? [];
        if ($storageOptions && ! in_array($validated['storage'] ?? null, $storageOptions, true)) {
            return back()->withErrors(['storage' => 'Please select the storage capacity.'])->withInput();
        }

        $invoicePath = null;
        if ($request->hasFile('invoice')) {
            $invoicePath = $request->file('invoice')->store('invoices', 'public');
        }

        $device = Device::create([
            'current_owner_id' => $request->user()->id,
            'imei' => $validated['imei'],
            'imei2' => $validated['imei2'] ?? null,
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'storage' => $storageOptions ? $validated['storage'] : null,
            'device_type' => $validated['product'],
            'purchase_type' => $validated['purchase_type'],
            'purchase_date' => $validated['purchase_type'] === 'new' ? $validated['purchase_date'] : null,
            'device_age' => $validated['purchase_type'] === 'secondhand' ? $validated['device_age'] : null,
            'invoice_path' => $invoicePath,
            'status' => 'active',
        ]);

        $userPackage->increment('used_device_count');

        $deviceLabel = trim($device->brand.' '.$device->model.' '.$device->storage);
        $subject = 'Device registered';
        $message = "Your device {$deviceLabel} (IMEI {$device->imei}) has been registered successfully.";
        $notifier->sendEmail($request->user()->email, $subject, $message);
        $notifier->sendSms($request->user()->mobile, $message);

        return redirect()
            ->route('devices.show', $device)
            ->with('status', 'Device registered successfully.');
    }

    protected function packageError($userPackage): ?string
    {
        if (! $userPackage) {
            return 'You need an active package to register devices.';
        }

        if ($userPackage->remainingDevices() <= 0) {
            return 'Your device limit has been reached. Please upgrade your package.';
        }

        return null;
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

            $device = Device::with('currentOwner')
                ->where('imei', $imei)
                ->orWhere('imei2', $imei)
                ->first();
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
