<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-background text-foreground">
            @include('partials.header')

            <!-- Page Content -->
            <main>
                {{ $slot ?? '' }}
                @yield('content')
            </main>

            @include('partials.footer')
        </div>
        <script>
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

                        const proceed = () => {
                            form.dataset.swalConfirmed = 'true';
                            form.submit();
                        };

                        if (window.Swal) {
                            Swal.fire({
                                title,
                                text,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: confirmText,
                                cancelButtonText: cancelText,
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
    </body>
</html>
