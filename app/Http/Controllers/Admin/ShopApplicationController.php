<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopApplication;
use App\Models\User;
use Illuminate\Http\Request;

class ShopApplicationController extends Controller
{
    public function index()
    {
        $applications = ShopApplication::with(['user', 'reviewer'])
            ->latest()
            ->paginate(20);

        return view('admin.shop-applications.index', compact('applications'));
    }

    public function update(Request $request, ShopApplication $application)
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'review_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->update([
            'status' => $data['status'],
            'review_notes' => $data['review_notes'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        if ($data['status'] === 'approved') {
            $application->user->update(['role' => User::ROLE_SHOP]);
        }

        return back()->with('status', 'Shop application updated.');
    }
}
