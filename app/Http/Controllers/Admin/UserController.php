<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $users = User::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users', 'settings', 'footerLinks'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:' . implode(',', [User::ROLE_USER, User::ROLE_SHOP, User::ROLE_ADMIN, User::ROLE_AUTHORITY])],
        ]);

        $user->update(['role' => $data['role']]);

        return redirect()->route('admin.users.index')->with('status', 'User role updated.');
    }
}
