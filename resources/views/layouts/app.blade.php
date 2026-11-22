<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#f8f7f4">

        @php $siteName = $settings?->app_name ?? config('app.name', 'DRMS'); @endphp
        <title>@hasSection('title')@yield('title') · @endif{{ $siteName }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="site antialiased">
        <div class="flex min-h-screen flex-col">
            @include('partials.header')
            @include('partials.flash')

            <main class="flex-1 @auth pb-24 md:pb-0 @endauth">
                {{ $slot ?? '' }}
                @yield('content')
            </main>

            @include('partials.footer')
        </div>

        @auth
            @include('partials.mobile-nav')
        @endauth

        <script>
            window.siteSwal = (options) => window.Swal.fire({
                confirmButtonColor: '#2b3cab',
                cancelButtonColor: '#ffffff',
                reverseButtons: true,
                customClass: { popup: 'rounded-2xl', cancelButton: 'swal-cancel' },
                ...options,
            });

            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('form[data-swal-confirm]').forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        if (form.dataset.swalConfirmed === 'true') {
                            return;
                        }
                        event.preventDefault();

                        const title = form.dataset.swalTitle || 'Are you sure?';
                        const text = form.dataset.swalText || '';
                        const confirmText = form.dataset.swalConfirm || 'Yes, continue';
                        const cancelText = form.dataset.swalCancel || 'Cancel';
                        const danger = form.dataset.swalDanger === 'true';

                        const proceed = () => {
                            form.dataset.swalConfirmed = 'true';
                            form.submit();
                        };

                        if (window.Swal) {
                            siteSwal({
                                title,
                                text,
                                icon: danger ? 'warning' : 'question',
                                showCancelButton: true,
                                confirmButtonText: confirmText,
                                cancelButtonText: cancelText,
                                ...(danger ? { confirmButtonColor: '#c42b3c' } : {}),
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    proceed();
                                }
                            });
                        } else if (confirm(`${title}${text ? '\n' + text : ''}`)) {
                            proceed();
                        }
                    });
                });
            });
        </script>
        <style>
            .swal2-popup { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; border-radius: 1.25rem; }
            .swal2-title { font-size: 1.25rem; color: #181b2e; }
            .swal2-html-container { color: #63667a; font-size: .95rem; }
            .swal2-styled { border-radius: .75rem !important; font-weight: 600 !important; padding: .7rem 1.25rem !important; }
            .swal2-styled.swal-cancel { color: #181b2e !important; border: 1px solid #dcd9d2 !important; }
            .swal2-input { border-radius: .75rem !important; }
        </style>
        @include('partials.image-preview')
        @stack('scripts')
    </body>
</html>
