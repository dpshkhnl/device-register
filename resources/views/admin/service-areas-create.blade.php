@extends('admin.layouts.app')

@section('page_heading', 'Add Service Area')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Add Service Area</h1>
                <p class="text-sm text-slate-500">Define country name and dial code.</p>
            </div>
            <a href="{{ route('admin.service-areas.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Back</a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.service-areas.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="text-sm font-medium text-slate-700">Country Name</label>
                    <input name="name" value="{{ old('name') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Nepal" required>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">ISO (2-letter)</label>
                        <input name="iso2" value="{{ old('iso2') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm uppercase" placeholder="NP" required>
                        <x-input-error :messages="$errors->get('iso2')" class="mt-2" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Dial Code</label>
                        <input name="dial_code" value="{{ old('dial_code') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="+977" required>
                        <x-input-error :messages="$errors->get('dial_code')" class="mt-2" />
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Sort Order</label>
                        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                    </div>
                    <label class="mt-6 inline-flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" {{ old('is_active', true) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Auth Options</p>
                    <div class="mt-3 grid gap-4 sm:grid-cols-2">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="allow_email_login" value="1" class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" {{ old('allow_email_login', true) ? 'checked' : '' }}>
                            Enable Email Login
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="require_email_otp" value="1" class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" {{ old('require_email_otp') ? 'checked' : '' }}>
                            Require Email OTP
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="allow_phone_login" value="1" class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" {{ old('allow_phone_login', true) ? 'checked' : '' }}>
                            Enable Phone Login
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="require_phone_otp" value="1" class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" {{ old('require_phone_otp') ? 'checked' : '' }}>
                            Require Phone OTP
                        </label>
                    </div>
                </div>
                <button class="rounded-lg bg-[color:var(--brand-600)] px-4 py-2 text-sm font-semibold text-white">Create Service Area</button>
            </form>
        </div>
    </div>
</div>
@endsection
