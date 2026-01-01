@extends('admin.layouts.app')

@section('page_heading', 'IMEI Lookup')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">IMEI Lookup</h1>
                    <p class="text-sm text-slate-500">Search a registered IMEI to view device details.</p>
                </div>
                <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-500">Admin Registry</span>
            </div>

            <form method="GET" action="{{ route('admin.imei-lookup') }}" class="mt-6 grid gap-3 md:grid-cols-12">
                <div class="md:col-span-9">
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">IMEI Number</label>
                    <input
                        type="text"
                        name="imei"
                        value="{{ old('imei', $imei ?? '') }}"
                        placeholder="Enter 15-digit IMEI"
                        inputmode="numeric"
                        pattern="[0-9]{15}"
                        minlength="15"
                        maxlength="15"
                        class="mt-2 h-12 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm font-mono"
                    />
                    @error('imei')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-end md:col-span-3">
                    <button class="h-12 w-full rounded-lg bg-[color:var(--brand-600)] px-4 text-sm font-semibold text-white">Search</button>
                </div>
            </form>
        </div>

        @if ($result === 'found')
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-6">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Device Found</h2>
                            <p class="text-xs text-slate-500">Verified on {{ now()->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Verified</span>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    <div class="rounded-xl bg-white px-4 py-3">
                        <p class="text-xs text-slate-500">IMEI</p>
                        <p class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $device->imei }}</p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3">
                        <p class="text-xs text-slate-500">IMEI 2</p>
                        <p class="mt-1 font-mono text-sm font-semibold text-slate-900">{{ $device->imei2 ?? 'Not provided' }}</p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3">
                        <p class="text-xs text-slate-500">Device</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $device->brand }} {{ $device->model }}</p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3">
                        <p class="text-xs text-slate-500">Status</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ ucfirst($device->status) }}</p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3">
                        <p class="text-xs text-slate-500">Owner</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $device->currentOwner?->name ?? 'Registered User' }}</p>
                    </div>
                    <div class="rounded-xl bg-white px-4 py-3">
                        <p class="text-xs text-slate-500">Registered</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $device->created_at?->format('M d, Y') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @elseif ($result === 'not_found')
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">No device found</h2>
                        <p class="text-xs text-slate-500">This IMEI is not registered in the system.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
