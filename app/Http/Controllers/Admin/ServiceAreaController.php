<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceArea;
use Illuminate\Http\Request;

class ServiceAreaController extends Controller
{
    public function index()
    {
        $serviceAreas = ServiceArea::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.service-areas-index', compact('serviceAreas'));
    }

    public function create()
    {
        return view('admin.service-areas-create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'iso2' => ['required', 'string', 'size:2', 'unique:service_areas,iso2'],
            'dial_code' => ['required', 'string', 'max:10'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['iso2'] = strtoupper($data['iso2']);
        $data['dial_code'] = trim($data['dial_code']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        ServiceArea::create($data);

        return redirect()->route('admin.service-areas.index')->with('status', 'Service area created.');
    }

    public function edit(ServiceArea $serviceArea)
    {
        return view('admin.service-areas-edit', compact('serviceArea'));
    }

    public function update(Request $request, ServiceArea $serviceArea)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'iso2' => ['required', 'string', 'size:2', 'unique:service_areas,iso2,'.$serviceArea->id],
            'dial_code' => ['required', 'string', 'max:10'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['iso2'] = strtoupper($data['iso2']);
        $data['dial_code'] = trim($data['dial_code']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $serviceArea->update($data);

        return redirect()->route('admin.service-areas.index')->with('status', 'Service area updated.');
    }

    public function destroy(ServiceArea $serviceArea)
    {
        $serviceArea->delete();

        return redirect()->route('admin.service-areas.index')->with('status', 'Service area deleted.');
    }
}
