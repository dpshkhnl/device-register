@extends('admin.layouts.app')

@section('page_heading', 'Security & Access')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft">
            <h2 class="text-lg font-semibold text-foreground">Authentication policies</h2>
            <p class="text-sm text-muted-foreground">Adjust login rules and admin security requirements.</p>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="text-sm font-medium text-foreground">OTP expiration window</label>
                    <input class="mt-2 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm" value="5 minutes" />
                </div>
                <div>
                    <label class="text-sm font-medium text-foreground">Failed login lockout</label>
                    <input class="mt-2 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm" value="5 attempts" />
                </div>
                <div class="flex items-center justify-between rounded-lg border border-border px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-foreground">Require MFA for admins</p>
                        <p class="text-xs text-muted-foreground">Applies to admin and authority roles.</p>
                    </div>
                    <input type="checkbox" checked class="h-4 w-4" />
                </div>
                <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground">Save policies</button>
            </div>
        </div>
        <div class="rounded-2xl border border-border bg-card p-6 shadow-soft">
            <h2 class="text-lg font-semibold text-foreground">Access auditing</h2>
            <p class="text-sm text-muted-foreground">Monitor privileged access and approvals.</p>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="text-sm font-medium text-foreground">High-risk action approval</label>
                    <input class="mt-2 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm" value="2 admin approvals" />
                </div>
                <div class="flex items-center justify-between rounded-lg border border-border px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-foreground">Log privileged sessions</p>
                        <p class="text-xs text-muted-foreground">Capture admin actions for audits.</p>
                    </div>
                    <input type="checkbox" checked class="h-4 w-4" />
                </div>
                <button class="rounded-lg border border-border px-4 py-2 text-sm font-semibold text-muted-foreground">Update auditing</button>
            </div>
        </div>
    </div>
</div>
@endsection
