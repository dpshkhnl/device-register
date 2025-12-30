<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\HomeBanner;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeBannerController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $banners = HomeBanner::orderBy('sort_order')->get();

        return view('admin.home-banners.index', compact('banners', 'settings', 'footerLinks'));
    }

    public function create()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.home-banners.create', compact('settings', 'footerLinks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'link_label' => ['nullable', 'string', 'max:60'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image_url'] = $request->file('image')->store('banners', 'public');
        }

        HomeBanner::create($validated);

        return redirect()->route('admin.home-banners.index')->with('status', 'Banner added successfully.');
    }

    public function edit(HomeBanner $homeBanner)
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.home-banners.edit', [
            'banner' => $homeBanner,
            'settings' => $settings,
            'footerLinks' => $footerLinks,
        ]);
    }

    public function update(Request $request, HomeBanner $homeBanner)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'link_label' => ['nullable', 'string', 'max:60'],
            'link_url' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($homeBanner->image_url) {
                Storage::disk('public')->delete($homeBanner->image_url);
            }
            $validated['image_url'] = $request->file('image')->store('banners', 'public');
        }

        $homeBanner->update($validated);

        return redirect()->route('admin.home-banners.index')->with('status', 'Banner updated successfully.');
    }

    public function destroy(HomeBanner $homeBanner)
    {
        $homeBanner->delete();

        return redirect()->route('admin.home-banners.index')->with('status', 'Banner deleted successfully.');
    }
}
