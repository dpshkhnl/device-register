<?php

use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeviceController as AdminDeviceController;
use App\Http\Controllers\Admin\FooterLinkController as AdminFooterLinkController;
use App\Http\Controllers\Admin\HomeFeatureController as AdminHomeFeatureController;
use App\Http\Controllers\Admin\HomeBannerController as AdminHomeBannerController;
use App\Http\Controllers\Admin\HomeStatController as AdminHomeStatController;
use App\Http\Controllers\Admin\HomeStepController as AdminHomeStepController;
use App\Http\Controllers\Admin\LostReportController as AdminLostReportController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\ShopApplicationController as AdminShopApplicationController;
use App\Http\Controllers\Admin\ServiceAreaController as AdminServiceAreaController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LostFoundController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopApplicationController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\TransferController;
use App\Models\Certificate;
use App\Models\FooterLink;
use App\Models\ImeiCheck;
use App\Models\SystemSetting;
use App\Models\TransferRequest;
use App\Models\User;
use App\Models\Device;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/verification', [DeviceController::class, 'verification'])->name('verification');
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/register-device', [DeviceController::class, 'create'])->name('register-device');
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');
    Route::get('/transfer', [TransferController::class, 'create'])->name('transfer');
    Route::post('/transfer/otp', [TransferController::class, 'sendOtp'])->name('transfer.otp');
    Route::post('/transfer/verify-new', [TransferController::class, 'verifyNewOtp'])->name('transfer.verify.new');
    Route::post('/transfer/verify-old', [TransferController::class, 'verifyOldOtp'])->name('transfer.verify.old');
    Route::post('/transfer', [TransferController::class, 'store'])->name('transfer.store');
    Route::post('/transfer/{transfer}/accept', [TransferController::class, 'accept'])->name('transfer.accept');
    Route::post('/transfer/{transfer}/cancel', [TransferController::class, 'cancel'])->name('transfer.cancel');
    Route::get('/lost-found', [LostFoundController::class, 'index'])->name('lost-found.index');
    Route::post('/lost-found', [LostFoundController::class, 'store'])->name('lost-found.store');
    Route::post('/packages/{package}/purchase', [PackageController::class, 'purchase'])->name('packages.purchase');
    Route::post('/shop-applications', [ShopApplicationController::class, 'store'])->name('shop-applications.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');

    Route::get('devices', [AdminDeviceController::class, 'index'])->name('devices.index');
    Route::get('devices/create', [AdminDeviceController::class, 'create'])->name('devices.create');
    Route::post('devices', [AdminDeviceController::class, 'store'])->name('devices.store');
    Route::get('devices/import', [AdminDeviceController::class, 'import'])->name('devices.import');
    Route::get('devices/import/template', [AdminDeviceController::class, 'downloadTemplate'])->name('devices.import.template');
    Route::post('devices/import', [AdminDeviceController::class, 'importStore'])->name('devices.import.store');
    Route::get('devices/{device}/edit', [AdminDeviceController::class, 'edit'])->name('devices.edit');
    Route::put('devices/{device}/details', [AdminDeviceController::class, 'updateDetails'])->name('devices.update-details');
    Route::get('devices/{device}', [AdminDeviceController::class, 'show'])->name('devices.show');
    Route::get('devices-export', [AdminDeviceController::class, 'export'])->name('devices.export');
    Route::delete('devices/{device}', [AdminDeviceController::class, 'destroy'])->name('devices.destroy');
    Route::put('devices/{device}', [AdminDeviceController::class, 'update'])->name('devices.update');

    Route::resource('home-features', AdminHomeFeatureController::class)->except('show');
    Route::resource('home-steps', AdminHomeStepController::class)->except('show');
    Route::resource('home-banners', AdminHomeBannerController::class)->except('show');
    Route::resource('home-stats', AdminHomeStatController::class)->except('show');
    Route::resource('testimonials', AdminTestimonialController::class)->except('show');
    Route::resource('footer-links', AdminFooterLinkController::class)->except('show');
    Route::resource('packages', AdminPackageController::class)->except('show');
    Route::resource('service-areas', AdminServiceAreaController::class)->except('show');
    Route::resource('brands', AdminBrandController::class)->except('show');

    Route::get('lost-stolen', [AdminLostReportController::class, 'index'])->name('lost-stolen.index');
    Route::put('lost-stolen/{report}', [AdminLostReportController::class, 'update'])->name('lost-stolen.update');

    Route::get('shop-applications', [AdminShopApplicationController::class, 'index'])->name('shop-applications.index');
    Route::put('shop-applications/{application}', [AdminShopApplicationController::class, 'update'])->name('shop-applications.update');

    Route::get('activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::get('imei-lookup', function () {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $imei = request('imei');
        $device = null;
        $result = null;

        if ($imei !== null && $imei !== '') {
            request()->validate([
                'imei' => ['required', 'digits:15'],
            ]);

            $device = Device::with('currentOwner')
                ->where('imei', $imei)
                ->orWhere('imei2', $imei)
                ->first();
            $result = $device ? 'found' : 'not_found';
        }

        return view('admin.imei-lookup', compact('settings', 'footerLinks', 'device', 'result', 'imei'));
    })->name('imei-lookup');

    Route::get('roles', function () {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $roles = [
            [
                'label' => 'Admin',
                'count' => User::where('role', 'admin')->count(),
                'permissions' => 'Full system control and oversight',
            ],
            [
                'label' => 'Authority',
                'count' => User::where('role', 'authority')->count(),
                'permissions' => 'Read-only access to lost/stolen records',
            ],
            [
                'label' => 'Shop / Reseller',
                'count' => User::where('role', 'shop')->count(),
                'permissions' => 'Verification and registration services',
            ],
            [
                'label' => 'Registered User',
                'count' => User::where('role', 'user')->count(),
                'permissions' => 'Register devices and manage transfers',
            ],
        ];

        $roleStats = [
            'total_roles' => count($roles),
            'pending_requests' => max(0, User::whereNull('email_verified_at')->count()),
            'admin_count' => User::where('role', 'admin')->count(),
        ];

        return view('admin.roles.index', compact('roles', 'roleStats', 'settings', 'footerLinks'));
    })->name('roles.index');

    Route::get('imei-logs', function () {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $search = request('q');

        $imeiLogs = ImeiCheck::with('checkedBy')
            ->when($search, fn($query) => $query->where('imei', 'like', "%{$search}%"))
            ->latest()
            ->paginate(10)
            ->appends(['q' => $search]);

        return view('admin.imei-logs.index', compact('imeiLogs', 'settings', 'footerLinks'));
    })->name('imei-logs.index');

    Route::get('certificates', function () {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $certificates = Certificate::with(['device', 'issuedTo'])->latest()->paginate(10);
        $pendingRequests = max(0, TransferRequest::where('status', 'pending')->count());

        return view('admin.certificates.index', compact('certificates', 'pendingRequests', 'settings', 'footerLinks'));
    })->name('certificates.index');

    Route::get('reports', function () {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $reportStats = [
            'generated' => \App\Models\Device::count(),
            'scheduled' => 12,
            'shared' => 6,
            'pending' => TransferRequest::where('status', 'pending')->count(),
        ];

        $reportCards = [
            [
                'title' => 'Devices by Status',
                'description' => 'Active, transferred, lost, suspicious distribution.',
                'action' => 'Open Report',
            ],
            [
                'title' => 'IMEI Verification Logs',
                'description' => 'Search volumes, success rate, anomalies.',
                'action' => 'View Logs',
            ],
            [
                'title' => 'Lost / Stolen Reports',
                'description' => 'Trending reports and response times.',
                'action' => 'Review',
            ],
        ];

        return view('admin.reports.index', compact('reportStats', 'reportCards', 'settings', 'footerLinks'));
    })->name('reports.index');

    Route::get('security', function () {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        return view('admin.security.index', compact('settings', 'footerLinks'));
    })->name('security.index');
    Route::put('security', [SystemSettingController::class, 'updateSecurity'])->name('security.update');

    Route::get('settings', [SystemSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SystemSettingController::class, 'update'])->name('settings.update');
    Route::post('settings/test-email', [SystemSettingController::class, 'testEmail'])->name('settings.test-email');
    Route::post('settings/test-sms', [SystemSettingController::class, 'testSms'])->name('settings.test-sms');
});

require __DIR__.'/auth.php';
