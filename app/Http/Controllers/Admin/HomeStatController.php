<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\HomeStat;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class HomeStatController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $stats = HomeStat::orderBy('sort_order')->get();

        return view('admin.home-stats.index', compact('stats', 'settings', 'footerLinks'));
    }

    public function create()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.home-stats.create', compact('settings', 'footerLinks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'value' => ['required', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        HomeStat::create($validated);

        return redirect()->route('admin.home-stats.index')->with('status', 'Stat added successfully.');
    }

    public function edit(HomeStat $homeStat)
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.home-stats.edit', [
            'stat' => $homeStat,
            'settings' => $settings,
            'footerLinks' => $footerLinks,
        ]);
    }

    public function update(Request $request, HomeStat $homeStat)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'value' => ['required', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $homeStat->update($validated);

        return redirect()->route('admin.home-stats.index')->with('status', 'Stat updated successfully.');
    }

    public function destroy(HomeStat $homeStat)
    {
        $homeStat->delete();

        return redirect()->route('admin.home-stats.index')->with('status', 'Stat deleted successfully.');
    }
}
