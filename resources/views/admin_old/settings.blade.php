@extends('admin.layouts.app')

@section('page_heading', 'Settings')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl border border-border bg-card p-6 shadow-soft">
        <p class="text-xs font-semibold uppercase text-muted-foreground">System Configuration</p>
        <h1 class="mt-2 text-2xl font-bold text-foreground">Settings</h1>
        <p class="mt-2 text-sm text-muted-foreground">Manage homepage content, branding, and operational defaults.</p>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft">
            <h2 class="text-lg font-semibold text-foreground">System preferences</h2>
            <p class="text-sm text-muted-foreground">Global settings for notifications and verification limits.</p>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="text-sm font-medium text-foreground">IMEI verification rate limit</label>
                    <input class="mt-2 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm" value="60 requests / hour" />
                </div>
                <div class="flex items-center justify-between rounded-lg border border-border px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-foreground">Enable OTP for sensitive actions</p>
                        <p class="text-xs text-muted-foreground">Transfers and status changes require OTP.</p>
                    </div>
                    <input type="checkbox" checked class="h-4 w-4" />
                </div>
                <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground">Save settings</button>
            </div>
        </div>
        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft">
            <h2 class="text-lg font-semibold text-foreground">Notification routing</h2>
            <p class="text-sm text-muted-foreground">Configure how alerts are distributed to admins.</p>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="text-sm font-medium text-foreground">Primary admin email</label>
                    <input class="mt-2 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm" value="security@drms.gov" />
                </div>
                <div>
                    <label class="text-sm font-medium text-foreground">Emergency SMS number</label>
                    <input class="mt-2 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm" value="+1 555 010 9922" />
                </div>
                <button class="rounded-lg border border-border px-4 py-2 text-sm font-semibold text-muted-foreground">Update routing</button>
            </div>
        </div>
    </div>
</div>
@endsection
