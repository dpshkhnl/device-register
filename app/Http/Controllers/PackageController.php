<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\UserPackage;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::where('is_active', true)
            ->orderByDesc('is_trial')
            ->orderBy('price')
            ->get();

        $activePackage = $request->user()?->activePackage()->with('package')->first();

        return view('pages.packages-index', compact('packages', 'activePackage'));
    }

    public function purchase(Request $request, Package $package)
    {
        if (! $package->is_active) {
            return back()->withErrors(['package' => 'This package is not available.']);
        }

        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $user->userPackages()
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $endsAt = $package->duration_days
            ? now()->addDays($package->duration_days)
            : null;

        UserPackage::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'device_limit' => $package->device_limit,
            'imei_limit' => $package->imei_limit,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'status' => 'active',
        ]);

        return redirect()->route('packages.index')->with('status', 'Package activated.');
    }
}
