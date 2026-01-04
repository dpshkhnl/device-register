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

        $search = request('q');

        $users = User::with(['activePackage.package'])
            ->when($search, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends(['q' => $search]);

        return view('admin.users.index', compact('users', 'settings', 'footerLinks'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:' . implode(',', [User::ROLE_USER, User::ROLE_SHOP, User::ROLE_ADMIN])],
        ]);

        $user->update(['role' => $data['role']]);

        return redirect()->route('admin.users.index')->with('status', 'User role updated.');
    }
}
