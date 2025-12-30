<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Login - {{ $settings?->app_name ?? 'DRMS' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-100">
            <div class="mb-6 text-center">
                <div class="mx-auto mb-4 h-14 w-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-semibold">
                    {{ strtoupper(substr($settings?->app_name ?? 'DRMS', 0, 1)) }}
                </div>
                <h1 class="text-xl font-semibold">Admin Login</h1>
                <p class="text-sm text-slate-500">Access the DRMS admin portal.</p>
            </div>

            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-semibold text-slate-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="admin@example.com">
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Password</label>
                    <input type="password" name="password" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="••••••••">
                    @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
