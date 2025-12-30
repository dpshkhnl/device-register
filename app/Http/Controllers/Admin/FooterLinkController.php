<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class FooterLinkController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $links = FooterLink::orderBy('group')->orderBy('sort_order')->get();

        return view('admin.footer-links.index', compact('links', 'settings'));
    }

    public function create()
    {
        $settings = SystemSetting::first();

        return view('admin.footer-links.create', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group' => ['required', 'string', 'max:60'],
            'label' => ['required', 'string', 'max:120'],
            'url' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        FooterLink::create($validated);

        return redirect()->route('admin.footer-links.index')->with('status', 'Footer link added successfully.');
    }

    public function edit(FooterLink $footerLink)
    {
        $settings = SystemSetting::first();

        return view('admin.footer-links.edit', [
            'link' => $footerLink,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request, FooterLink $footerLink)
    {
        $validated = $request->validate([
            'group' => ['required', 'string', 'max:60'],
            'label' => ['required', 'string', 'max:120'],
            'url' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $footerLink->update($validated);

        return redirect()->route('admin.footer-links.index')->with('status', 'Footer link updated successfully.');
    }

    public function destroy(FooterLink $footerLink)
    {
        $footerLink->delete();

        return redirect()->route('admin.footer-links.index')->with('status', 'Footer link deleted successfully.');
    }
}
