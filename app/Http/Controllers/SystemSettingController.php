<?php

namespace App\Http\Controllers;

use App\Http\Requests\SystemSettingRequest;
use App\Models\SystemSetting;

class SystemSettingController extends Controller
{
    public function edit()
    {
        $setting = SystemSetting::first() ?? SystemSetting::create([]);

        return view('admin.settings.edit', compact('setting'));
    }

    public function update(SystemSettingRequest $request)
    {
        $setting = SystemSetting::first() ?? SystemSetting::create([]);
        $setting->update($request->validated());

        return redirect()->route('admin.settings.edit')->with('status', 'Settings updated successfully.');
    }
}
