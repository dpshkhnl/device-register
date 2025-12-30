<?php

use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeviceController as AdminDeviceController;
use App\Http\Controllers\Admin\LostReportController as AdminLostReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SystemSettingController;
use App\Models\Certificate;
use App\Models\FooterLink;
use App\Models\ImeiCheck;
use App\Models\SystemSetting;
use App\Models\TransferRequest;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.home')->name('home');
Route::view('/verification', 'pages.verification')->name('verification');

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
    Route::view('/transfer', 'pages.transfer')->name('transfer');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');

    Route::get('devices', [AdminDeviceController::class, 'index'])->name('devices.index');
    Route::get('devices/{device}', [AdminDeviceController::class, 'show'])->name('devices.show');
    Route::put('devices/{device}', [AdminDeviceController::class, 'update'])->name('devices.update');

    Route::get('lost-stolen', [AdminLostReportController::class, 'index'])->name('lost-stolen.index');
    Route::put('lost-stolen/{report}', [AdminLostReportController::class, 'update'])->name('lost-stolen.update');

    Route::get('activity-logs', [AdminActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::get('transfers', function () {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $transfers = TransferRequest::with(['device', 'fromUser', 'toUser'])->latest()->paginate(10);

        return view('admin.transfers.index', compact('transfers', 'settings', 'footerLinks'));
    })->name('transfers.index');

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

        $imeiLogs = ImeiCheck::with('checkedBy')->latest()->paginate(10);

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

    Route::get('tasks', function () {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $taskStats = [
            'queued' => 16,
            'processing' => 3,
            'failed' => 1,
        ];

        $tasks = [
            ['name' => 'IMEI verification ingest', 'status' => 'processing', 'updated_at' => now()->subMinutes(10)],
            ['name' => 'Lost device alert dispatch', 'status' => 'queued', 'updated_at' => now()->subMinutes(40)],
            ['name' => 'Certificate generation batch', 'status' => 'queued', 'updated_at' => now()->subHour()],
            ['name' => 'Daily compliance export', 'status' => 'failed', 'updated_at' => now()->subHours(2)],
        ];

        return view('admin.tasks.index', compact('tasks', 'taskStats', 'settings', 'footerLinks'));
    })->name('tasks.index');

    Route::get('security', function () {
        $settings = SystemSetting::first();
        $footerLinks = FooterLink::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $policies = [
            ['label' => 'MFA required for admins', 'value' => 'Enabled'],
            ['label' => 'Session timeout', 'value' => '30 minutes'],
            ['label' => 'Failed login alerts', 'value' => 'Enabled'],
            ['label' => 'Password rotation', 'value' => 'Every 90 days'],
        ];

        return view('admin.security.index', compact('policies', 'settings', 'footerLinks'));
    })->name('security.index');

    Route::get('settings', [SystemSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SystemSettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
