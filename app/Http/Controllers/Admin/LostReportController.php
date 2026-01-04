<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\LostReport;
use App\Models\SystemSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class LostReportController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        LostReport::with('device')
            ->where('type', 'found')
            ->whereHas('device', fn ($query) => $query->where('status', '!=', 'active'))
            ->get()
            ->each(function ($report) {
                if ($report->device) {
                    $report->device->update(['status' => 'active']);
                }
            });

        $reports = LostReport::with('device', 'reporter')
            ->where('type', 'lost')
            ->latest()
            ->paginate(20);

        return view('admin.lost-stolen.index', compact('reports', 'settings', 'footerLinks'));
    }

    public function update(Request $request, LostReport $report, ActivityLogger $logger)
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        $report->status = $data['status'];
        $report->approved_by = $request->user()->id;
        $report->approved_at = now();
        $report->save();

        if ($data['status'] === 'approved' && $report->device) {
            $report->device->update([
                'status' => $report->type === 'found' ? 'active' : 'lost',
            ]);
        }

        $logger->log('lost_report_' . $data['status'], $report, [], [
            'status' => $data['status'],
            'device_id' => $report->device_id,
        ], $request->user()->id);

        return redirect()->route('admin.lost-stolen.index')->with('status', 'Report updated.');
    }
}
