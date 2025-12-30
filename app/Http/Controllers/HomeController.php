<?php

namespace App\Http\Controllers;

use App\Models\HomeFeature;
use App\Models\HomeBanner;
use App\Models\HomeStat;
use App\Models\HomeStep;
use App\Models\SystemSetting;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::first();
        $banners = HomeBanner::where('is_active', true)->orderBy('sort_order')->get();
        $features = HomeFeature::where('is_active', true)->orderBy('sort_order')->get();
        $steps = HomeStep::where('is_active', true)->orderBy('sort_order')->get();
        $stats = HomeStat::where('is_active', true)->orderBy('sort_order')->get();
        $testimonials = Testimonial::where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.home', compact('settings', 'banners', 'features', 'steps', 'stats', 'testimonials'));
    }
}
