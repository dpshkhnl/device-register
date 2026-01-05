<?php

namespace App\Http\Controllers;

use App\Models\ShopApplication;
use Illuminate\Http\Request;

class ShopApplicationController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'shop') {
            return back()->withErrors(['shop_application' => 'You are already a shop.']);
        }

        $existing = ShopApplication::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
        if ($existing) {
            return back()->withErrors(['shop_application' => 'Your shop application is already under review.']);
        }

        $data = $request->validate([
            'shop_name' => ['required', 'string', 'max:160'],
            'address' => ['required', 'string', 'max:255'],
            'business_registration' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'store_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $businessPath = $request->file('business_registration')->store('shop-applications', 'public');
        $storePath = $request->file('store_photo')->store('shop-applications', 'public');

        ShopApplication::create([
            'user_id' => $user->id,
            'shop_name' => $data['shop_name'],
            'address' => $data['address'],
            'business_registration_path' => $businessPath,
            'store_photo_path' => $storePath,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Shop application submitted for review.');
    }
}
