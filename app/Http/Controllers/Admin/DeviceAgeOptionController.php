<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceAgeOption;
use Illuminate\Http\Request;

class DeviceAgeOptionController extends Controller
{
    public function index()
    {
        $options = DeviceAgeOption::orderBy('sort_order')
            ->orderBy('label')
            ->get();

        return view('admin.device-ages.index', compact('options'));
    }

    public function create()
    {
        return view('admin.device-ages.create');
    }

    public function store(Request $request)
    {
        DeviceAgeOption::create($this->validated($request));

        return redirect()->route('admin.device-ages.index')->with('status', 'Option created.');
    }

    public function edit(DeviceAgeOption $deviceAge)
    {
        return view('admin.device-ages.edit', ['option' => $deviceAge]);
    }

    public function update(Request $request, DeviceAgeOption $deviceAge)
    {
        $deviceAge->update($this->validated($request, $deviceAge));

        return redirect()->route('admin.device-ages.index')->with('status', 'Option updated.');
    }

    public function destroy(DeviceAgeOption $deviceAge)
    {
        $deviceAge->delete();

        return redirect()->route('admin.device-ages.index')->with('status', 'Option deleted.');
    }

    protected function validated(Request $request, ?DeviceAgeOption $option = null): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:60', 'unique:device_age_options,label'.($option ? ','.$option->id : '')],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['label'] = trim($data['label']);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }
}
