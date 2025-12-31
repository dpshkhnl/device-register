@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-background py-10 lg:py-14">
    <div class="pointer-events-none absolute inset-0 opacity-20"
        style="background-image: url('/images/shield-badge.svg'); background-repeat: no-repeat; background-position: right 6% top 12%; background-size: 200px;">
    </div>
    <div class="container max-w-5xl">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Lost / Found</h1>
                <p class="text-xs text-muted-foreground">Report lost devices and confirm when found.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
                </svg>
                Back to Dashboard
            </a>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid gap-4">
            @forelse ($devices as $device)
                @php
                    $deviceReports = $reports[$device->id] ?? collect();
                    $latestReport = $deviceReports->first();
                    $isLost = $device->status === 'lost';
                @endphp
                <div class="rounded-2xl border border-border bg-card p-5 shadow-soft">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-foreground">{{ $device->brand }} {{ $device->model }}</p>
                            <p class="text-xs text-muted-foreground">IMEI {{ $device->imei }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-muted px-3 py-1 text-xs font-semibold text-muted-foreground">
                            {{ ucfirst($device->status) }}
                        </span>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-4 border-t border-border pt-4">
                        <div class="text-xs text-muted-foreground">
                            @if ($latestReport)
                                Last report: {{ ucfirst($latestReport->type) }} ({{ ucfirst($latestReport->status) }})
                            @else
                                No reports yet.
                            @endif
                        </div>

                        <form method="POST" action="{{ route('lost-found.store') }}" class="flex flex-wrap items-center gap-3" data-swal-password data-swal-title="Confirm status change" data-swal-text="Enter your password to continue.">
                            @csrf
                            <input type="hidden" name="device_id" value="{{ $device->id }}">
                            <input type="hidden" name="action" value="{{ $isLost ? 'found' : 'lost' }}">
                            <input type="hidden" name="password" value="">
                            <input type="text" name="description" placeholder="Optional note" class="h-9 w-56 rounded-lg border border-border bg-background px-3 text-xs text-foreground" />
                            <button
                                class="inline-flex h-9 items-center justify-center rounded-lg px-4 text-xs font-semibold text-white {{ $isLost ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }}"
                            >
                                {{ $isLost ? 'Mark Found' : 'Report Lost' }}
                            </button>
                        </form>
                        @error('password')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-border bg-card p-10 text-center text-sm text-muted-foreground">
                    No registered devices found.
                </div>
            @endforelse
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form[data-swal-password]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.swalConfirmed === 'true') {
                    return;
                }
                event.preventDefault();

                const title = form.dataset.swalTitle || 'Confirm action';
                const text = form.dataset.swalText || '';

                if (!window.Swal) {
                    const password = prompt(`${title}\n${text}`);
                    if (!password) {
                        return;
                    }
                    form.querySelector('input[name="password"]').value = password;
                    form.dataset.swalConfirmed = 'true';
                    form.submit();
                    return;
                }

                Swal.fire({
                    title,
                    text,
                    icon: 'warning',
                    input: 'password',
                    inputLabel: 'Password',
                    inputPlaceholder: 'Enter your password',
                    inputAttributes: {
                        autocomplete: 'current-password',
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Continue',
                    cancelButtonText: 'Cancel',
                    preConfirm: (value) => {
                        if (!value) {
                            Swal.showValidationMessage('Password is required');
                            return false;
                        }
                        return value;
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.querySelector('input[name="password"]').value = result.value;
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection
