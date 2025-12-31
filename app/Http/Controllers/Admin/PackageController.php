<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\Package;
use App\Models\SystemSetting;
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

        $package->update($data);

        return redirect()->route('admin.packages.index')->with('status', 'Package updated.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')->with('status', 'Package deleted.');
    }
}
