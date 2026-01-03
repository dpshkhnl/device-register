@extends('admin.layouts.app')

@section('page_heading', 'Security & Access')

@section('content')
    <div class="w-full space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif
        <form method="POST" action="{{ route('admin.security.update') }}" class="grid gap-4 lg:grid-cols-2">
            @csrf
            @method('PUT')
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Authentication policies</h2>
                <p class="text-sm text-slate-500">Control OTP enforcement and resend timing.</p>
                <div class="mt-4 space-y-4">
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Force OTP verification</p>
                            <p class="text-xs text-slate-500">Require OTP for login and registration.</p>
                        </div>
                        <input type="checkbox" name="auth_force_otp" value="1" class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" {{ $settings?->auth_force_otp ? 'checked' : '' }}>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">OTP resend delay (seconds)</label>
                        <input name="otp_resend_seconds" type="number" min="10" max="600" value="{{ old('otp_resend_seconds', $settings?->otp_resend_seconds ?? 60) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <button class="rounded-lg bg-[color:var(--brand-600)] px-4 py-2 text-sm font-semibold text-white">Save policies</button>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Access auditing</h2>
                <p class="text-sm text-slate-500">Monitor privileged access and approvals.</p>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700">High-risk action approval</label>
                        <input class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" value="2 admin approvals" disabled>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Log privileged sessions</p>
                            <p class="text-xs text-slate-500">Capture admin actions for audits.</p>
                        </div>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" disabled>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Alert on role changes</p>
                            <p class="text-xs text-slate-500">Notify admins on access updates.</p>
                        </div>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" disabled>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
