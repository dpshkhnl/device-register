<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\HomeFeature;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class HomeFeatureController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $features = HomeFeature::orderBy('sort_order')->get();

        return view('admin.home-features.index', compact('features', 'settings', 'footerLinks'));
    }

    public function create()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.home-features.create', compact('settings', 'footerLinks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:80'],
            'button_label' => ['nullable', 'string', 'max:60'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        HomeFeature::create($validated);

        return redirect()->route('admin.home-features.index')->with('status', 'Feature added successfully.');
    }

    public function edit(HomeFeature $homeFeature)
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.home-features.edit', [
            'feature' => $homeFeature,
            'settings' => $settings,
            'footerLinks' => $footerLinks,
        ]);
    }

    public function update(Request $request, HomeFeature $homeFeature)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:80'],
            'button_label' => ['nullable', 'string', 'max:60'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $homeFeature->update($validated);

        return redirect()->route('admin.home-features.index')->with('status', 'Feature updated successfully.');
    }

    public function destroy(HomeFeature $homeFeature)
    {
        $homeFeature->delete();

        return redirect()->route('admin.home-features.index')->with('status', 'Feature deleted successfully.');
    }
}
