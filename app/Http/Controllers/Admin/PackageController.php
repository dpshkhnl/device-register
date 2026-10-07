<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\Package;
use App\Models\SystemSetting;
use App\Models\UserPackage;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $packages = Package::orderByDesc('is_trial')->orderBy('name')->paginate(20);

        return view('admin.packages-index', compact('packages', 'settings', 'footerLinks'));
    }

    public function create()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.packages-create', compact('settings', 'footerLinks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:800'],
            'price' => ['required', 'numeric', 'min:0'],
            'device_limit' => ['required', 'integer', 'min:0'],
            'imei_limit' => ['required', 'integer', 'min:0'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'is_trial' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_trial'] = (bool) ($data['is_trial'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        if ($data['is_trial']) {
            Package::where('is_trial', true)->update(['is_trial' => false]);
        }

        Package::create($data);

        return redirect()->route('admin.packages.index')->with('status', 'Package created.');
    }

    public function edit(Package $package)
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.packages-edit', compact('package', 'settings', 'footerLinks'));
    }

    public function update(Request $request, Package $package)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:800'],
            'price' => ['required', 'numeric', 'min:0'],
            'device_limit' => ['required', 'integer', 'min:0'],
            'imei_limit' => ['required', 'integer', 'min:0'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'is_trial' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_trial'] = (bool) ($data['is_trial'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        if ($data['is_trial']) {
            Package::where('is_trial', true)->where('id', '!=', $package->id)->update(['is_trial' => false]);
        }

        $durationChanged = (int) $package->duration_days !== (int) ($data['duration_days'] ?? 0);

        $package->update($data);

        // Limits are copied onto each customer's plan at purchase, so push the new values to current subscribers.
        $activePlans = UserPackage::where('package_id', $package->id)
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()));

        $synced = (clone $activePlans)->update([
            'device_limit' => $package->device_limit,
            'imei_limit' => $package->imei_limit,
        ]);

        if ($durationChanged) {
            $activePlans->each(fn (UserPackage $plan) => $plan->update([
                'ends_at' => $package->duration_days ? $plan->starts_at?->copy()->addDays($package->duration_days) : null,
            ]));
        }

        return redirect()->route('admin.packages.index')
            ->with('status', "Package updated. {$synced} active customer plan(s) updated.");
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')->with('status', 'Package deleted.');
    }
}
