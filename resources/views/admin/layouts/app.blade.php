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
    @if (session('status'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 4200)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed right-6 top-6 z-50 w-[min(92vw,22rem)] rounded-2xl border border-emerald-200 bg-white px-4 py-3 text-sm text-emerald-900 shadow-[0_18px_40px_-20px_rgba(15,23,42,0.6)]"
            role="status"
            aria-live="polite"
        >
            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-900">Status updated</p>
                    <p class="mt-1 text-sm text-slate-600">{{ session('status') }}</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600" x-on:click="show = false" aria-label="Close notification">
                    x
                </button>
            </div>
        </div>
    @endif

    <div class="min-h-screen flex">
        @include('admin.partials.sidebar')
        <div class="flex-1 flex flex-col">
            @include('admin.partials.topbar')
            <main class="flex-1 p-6">
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
