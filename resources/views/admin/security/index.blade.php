@extends('admin.layouts.app')

@section('page_heading', 'Security & Access')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Authentication policies</h2>
                <p class="text-sm text-slate-500">Adjust login rules and admin security requirements.</p>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700">OTP expiration window</label>
                        <input class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" value="5 minutes">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Failed login lockout</label>
                        <input class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" value="5 attempts">
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Require MFA for admins</p>
                            <p class="text-xs text-slate-500">Applies to admin and authority roles.</p>
                        </div>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]">
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
                        <input class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" value="2 admin approvals">
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Log privileged sessions</p>
                            <p class="text-xs text-slate-500">Capture admin actions for audits.</p>
                        </div>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]">
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Alert on role changes</p>
                            <p class="text-xs text-slate-500">Notify admins on access updates.</p>
                        </div>
                        <input type="checkbox" checked class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]">
                    </div>
                    <button class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Update auditing</button>
                </div>
            </div>
        </div>
    </div>
@endsection
