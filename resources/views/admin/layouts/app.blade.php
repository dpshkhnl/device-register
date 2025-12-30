<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', ($settings?->app_name ?? 'DRMS') . ' Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $settings = $settings ?? \App\Models\SystemSetting::first();
    @endphp
    <meta name="theme-color" content="{{ $settings?->brand_primary ?? '#1d4ed8' }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --brand-100: {{ $settings?->brand_secondary ?? '#dbeafe' }};
            --brand-600: {{ $settings?->brand_primary ?? '#1d4ed8' }};
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen flex">
        @include('admin.partials.sidebar')
        <div class="flex-1 flex flex-col">
            @include('admin.partials.topbar')
            <main class="flex-1 p-6">
                @if (session('status'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Please check the form for errors.
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
