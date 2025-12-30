<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'DRMS Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-foreground">
    <div class="flex min-h-screen">
        @include('admin.partials.sidebar')
        <div class="flex-1">
            @include('admin.partials.topbar')
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
