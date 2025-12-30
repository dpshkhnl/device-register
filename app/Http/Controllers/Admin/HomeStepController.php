<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\HomeStep;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class HomeStepController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $steps = HomeStep::orderBy('sort_order')->get();

        return view('admin.home-steps.index', compact('steps', 'settings', 'footerLinks'));
    }

    public function create()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.home-steps.create', compact('settings', 'footerLinks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        HomeStep::create($validated);

        return redirect()->route('admin.home-steps.index')->with('status', 'Step added successfully.');
    }

    public function edit(HomeStep $homeStep)
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.home-steps.edit', [
            'step' => $homeStep,
            'settings' => $settings,
            'footerLinks' => $footerLinks,
        ]);
    }

    public function update(Request $request, HomeStep $homeStep)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $homeStep->update($validated);

        return redirect()->route('admin.home-steps.index')->with('status', 'Step updated successfully.');
    }

    public function destroy(HomeStep $homeStep)
    {
        $homeStep->delete();

        return redirect()->route('admin.home-steps.index')->with('status', 'Step deleted successfully.');
    }
}
