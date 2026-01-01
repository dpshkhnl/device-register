<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Device;
use App\Models\FooterLink;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        $search = request('q');
        $status = request('status');

        $devices = Device::with('currentOwner')
            ->when($search, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                $inner->where('imei', 'like', "%{$search}%")
                        ->orWhere('imei2', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhereHas('currentOwner', function ($ownerQuery) use ($search) {
                            $ownerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status && $status !== 'all', fn($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->appends(['q' => $search, 'status' => $status]);

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

    public function create()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $deviceTypes = [
            ['value' => 'smartphone', 'label' => 'Smartphone'],
            ['value' => 'tablet', 'label' => 'Tablet'],
            ['value' => 'smartwatch', 'label' => 'Smartwatch'],
            ['value' => 'laptop', 'label' => 'Laptop'],
            ['value' => 'other', 'label' => 'Other'],
        ];

        $brands = Brand::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
        if (! $brands) {
            $brands = ['Other'];
        }

        $users = User::orderBy('name')->get();

        return view('admin.devices.create', compact('settings', 'footerLinks', 'deviceTypes', 'brands', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id' => ['required', 'exists:users,id'],
            'imei' => ['required', 'digits:15', 'unique:devices,imei'],
            'imei2' => ['nullable', 'digits:15', 'unique:devices,imei2', 'different:imei'],
            'brand' => ['required', 'string', 'max:100', 'exists:brands,name'],
            'model' => ['required', 'string', 'max:100'],
            'device_type' => ['required', 'string', 'max:50'],
            'purchase_type' => ['required', 'in:new,secondhand'],
            'purchase_date' => ['required', 'date'],
            'status' => ['required', 'in:active,transferred,lost,suspicious'],
            'seller_name' => ['nullable', 'string', 'max:100'],
            'invoice' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $invoicePath = null;
        if ($request->hasFile('invoice')) {
            $invoicePath = $request->file('invoice')->store('invoices', 'public');
        }

        $device = Device::create([
            'current_owner_id' => $validated['owner_id'],
            'imei' => $validated['imei'],
            'imei2' => $validated['imei2'] ?? null,
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'device_type' => $validated['device_type'],
            'purchase_type' => $validated['purchase_type'],
            'purchase_date' => $validated['purchase_date'],
            'status' => $validated['status'],
            'seller_name' => $validated['seller_name'],
            'invoice_path' => $invoicePath,
            'registered_at' => now(),
        ]);

        return redirect()
            ->route('admin.devices.show', $device)
            ->with('status', 'Device added successfully.');
    }

    public function edit(Device $device)
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $deviceTypes = [
            ['value' => 'smartphone', 'label' => 'Smartphone'],
            ['value' => 'tablet', 'label' => 'Tablet'],
            ['value' => 'smartwatch', 'label' => 'Smartwatch'],
            ['value' => 'laptop', 'label' => 'Laptop'],
            ['value' => 'other', 'label' => 'Other'],
        ];

        $brands = Brand::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
        if (! $brands) {
            $brands = ['Other'];
        }

        $users = User::orderBy('name')->get();

        return view('admin.devices.edit', compact('device', 'settings', 'footerLinks', 'deviceTypes', 'brands', 'users'));
    }

    public function updateDetails(Request $request, Device $device)
    {
        $validated = $request->validate([
            'owner_id' => ['required', 'exists:users,id'],
            'imei' => ['required', 'digits:15', Rule::unique('devices', 'imei')->ignore($device->id)],
            'imei2' => ['nullable', 'digits:15', Rule::unique('devices', 'imei2')->ignore($device->id), 'different:imei'],
            'brand' => ['required', 'string', 'max:100', 'exists:brands,name'],
            'model' => ['required', 'string', 'max:100'],
            'device_type' => ['required', 'string', 'max:50'],
            'purchase_type' => ['required', 'in:new,secondhand'],
            'purchase_date' => ['required', 'date'],
            'status' => ['required', 'in:active,transferred,lost,suspicious'],
            'seller_name' => ['nullable', 'string', 'max:100'],
            'invoice' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($request->hasFile('invoice')) {
            $validated['invoice_path'] = $request->file('invoice')->store('invoices', 'public');
        }

        $device->update([
            'current_owner_id' => $validated['owner_id'],
            'imei' => $validated['imei'],
            'imei2' => $validated['imei2'] ?? null,
            'brand' => $validated['brand'],
            'model' => $validated['model'],
            'device_type' => $validated['device_type'],
            'purchase_type' => $validated['purchase_type'],
            'purchase_date' => $validated['purchase_date'],
            'status' => $validated['status'],
            'seller_name' => $validated['seller_name'],
            'invoice_path' => $validated['invoice_path'] ?? $device->invoice_path,
        ]);

        return redirect()
            ->route('admin.devices.show', $device)
            ->with('status', 'Device updated successfully.');
    }

    public function import()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.devices.import', compact('settings', 'footerLinks'));
    }

    public function downloadTemplate()
    {
        $headers = [
            'owner_name',
            'owner_email',
            'owner_mobile',
            'imei',
            'imei2',
            'brand',
            'model',
            'device_type',
            'purchase_type',
            'purchase_date',
            'status',
            'seller_name',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');
        $brands = Brand::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name')
            ->take(5)
            ->toArray();
        $models = ['iPhone 15', 'Galaxy S24', 'Pixel 9', 'OnePlus 12', 'Xiaomi 14'];
        $deviceTypes = ['smartphone', 'tablet', 'smartwatch', 'laptop'];
        $purchaseTypes = ['new', 'secondhand'];
        $statuses = ['active', 'transferred', 'lost', 'suspicious'];

        if (! $brands) {
            $brands = ['Other'];
        }

        $rows = [];
        for ($i = 1; $i <= 100; $i++) {
            $rows[] = [
                'User '.$i,
                'user'.$i.'@example.com',
                '9800000'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                str_pad((string) $i, 15, '0', STR_PAD_LEFT),
                null,
                $brands[$i % count($brands)],
                $models[$i % count($models)],
                $deviceTypes[$i % count($deviceTypes)],
                $purchaseTypes[$i % count($purchaseTypes)],
                now()->subDays($i)->format('Y-m-d'),
                $statuses[$i % count($statuses)],
                'Sample Seller '.$i,
            ];
        }
        $sheet->fromArray($rows, null, 'A2');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'device-import-template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $path = $request->file('import_file')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows) || count($rows) < 2) {
            return back()->withErrors(['import_file' => 'The file is empty or missing rows.']);
        }

        $headerRow = $rows[1] ?? [];
        $headers = [];
        foreach ($headerRow as $column => $value) {
            $key = Str::of((string) $value)
                ->lower()
                ->replace([' ', '-'], '_')
                ->toString();
            if ($key !== '') {
                $headers[$key] = $column;
            }
        }

        $requiredHeaders = [
            'owner_name',
            'owner_email',
            'imei',
            'brand',
            'model',
            'device_type',
            'purchase_type',
            'purchase_date',
            'status',
        ];

        $missingHeaders = array_values(array_diff($requiredHeaders, array_keys($headers)));
        if ($missingHeaders) {
            return back()->withErrors([
                'import_file' => 'Missing required columns: '.implode(', ', $missingHeaders).'.',
            ]);
        }

        $successCount = 0;
        $errorRows = [];

        foreach (array_slice($rows, 1, null, true) as $rowIndex => $row) {
            $raw = [];
            foreach ($headers as $key => $column) {
                $raw[$key] = isset($row[$column]) ? trim((string) $row[$column]) : null;
            }

            $isEmpty = collect($raw)->every(fn($value) => $value === null || $value === '');
            if ($isEmpty) {
                continue;
            }

            $raw['purchase_type'] = strtolower($raw['purchase_type'] ?? '');
            $raw['device_type'] = strtolower($raw['device_type'] ?? '');
            $raw['status'] = strtolower($raw['status'] ?? '');

            $purchaseDate = $raw['purchase_date'] ?? null;
            if ($purchaseDate !== null && $purchaseDate !== '') {
                if (is_numeric($purchaseDate)) {
                    $purchaseDate = ExcelDate::excelToDateTimeObject((float) $purchaseDate)->format('Y-m-d');
                } else {
                    try {
                        $purchaseDate = Carbon::parse($purchaseDate)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        $purchaseDate = $purchaseDate;
                    }
                }
            }

            $validator = validator([
                'owner_name' => $raw['owner_name'] ?? null,
                'owner_email' => $raw['owner_email'] ?? null,
                'owner_mobile' => $raw['owner_mobile'] ?? null,
                'imei' => $raw['imei'] ?? null,
                'imei2' => $raw['imei2'] ?? null,
                'brand' => $raw['brand'] ?? null,
                'model' => $raw['model'] ?? null,
                'device_type' => $raw['device_type'] ?? null,
                'purchase_type' => $raw['purchase_type'] ?? null,
                'purchase_date' => $purchaseDate,
                'status' => $raw['status'] ?? null,
                'seller_name' => $raw['seller_name'] ?? null,
            ], [
                'owner_name' => ['required', 'string', 'max:255'],
                'owner_email' => ['required', 'email'],
                'owner_mobile' => ['nullable', 'string', 'max:20'],
                'imei' => ['required', 'digits:15', 'unique:devices,imei'],
                'imei2' => ['nullable', 'digits:15', 'unique:devices,imei2', 'different:imei'],
                'brand' => ['required', 'string', 'max:100', 'exists:brands,name'],
                'model' => ['required', 'string', 'max:100'],
                'device_type' => ['required', 'string', 'max:50'],
                'purchase_type' => ['required', 'in:new,secondhand'],
                'purchase_date' => ['required', 'date'],
                'status' => ['required', 'in:active,transferred,lost,suspicious'],
                'seller_name' => ['nullable', 'string', 'max:100'],
            ]);

            if ($validator->fails()) {
                $errorRows[] = [
                    'row' => $rowIndex,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            $email = strtolower($raw['owner_email']);
            $mobile = $raw['owner_mobile'] ?: null;
            if (! $mobile || User::where('mobile', $mobile)->exists()) {
                $mobile = $this->generateUniqueMobile();
            }

            $user = User::where('email', $email)->first();
            if (! $user) {
                $user = User::create([
                    'name' => $raw['owner_name'],
                    'email' => $email,
                    'mobile' => $mobile,
                    'role' => User::ROLE_USER,
                    'password' => Hash::make(Str::random(12)),
                ]);
            } elseif (! $user->mobile) {
                $user->update(['mobile' => $mobile]);
            }

            Device::create([
                'current_owner_id' => $user->id,
                'imei' => $raw['imei'],
                'imei2' => $raw['imei2'] ?? null,
                'brand' => $raw['brand'],
                'model' => $raw['model'],
                'device_type' => $raw['device_type'],
                'purchase_type' => $raw['purchase_type'],
                'purchase_date' => $purchaseDate,
                'status' => $raw['status'],
                'seller_name' => $raw['seller_name'] ?? null,
                'registered_at' => now(),
            ]);

            $successCount++;
        }

        $request->session()->flash('import_summary', [
            'success' => $successCount,
            'failed' => count($errorRows),
        ]);
        $request->session()->flash('import_errors', $errorRows);

        return redirect()->route('admin.devices.import');
    }

    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()
            ->route('admin.devices.index')
            ->with('status', 'Device deleted successfully.');
    }

    protected function generateUniqueMobile(): string
    {
        do {
            $mobile = '98'.str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (User::where('mobile', $mobile)->exists());

        return $mobile;
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
