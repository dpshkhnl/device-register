@extends('admin.layouts.app')

@section('page_heading', 'Settings')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">System Configuration</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Settings</h1>
            <p class="mt-2 text-sm text-slate-500">Manage homepage content, branding, and operational defaults.</p>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 space-y-8">
            @csrf
            @method('PUT')

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Brand</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="text-sm font-medium text-slate-700">App Name</label>
                        <input name="app_name" value="{{ old('app_name', $setting->app_name) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Primary Color</label>
                        <input name="brand_primary" value="{{ old('brand_primary', $setting->brand_primary) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Secondary Color</label>
                        <input name="brand_secondary" value="{{ old('brand_secondary', $setting->brand_secondary) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Hero</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Title</label>
                        <input name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Subtitle</label>
                        <textarea name="hero_subtitle" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="2">{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Primary Button Label</label>
                        <input name="hero_primary_label" value="{{ old('hero_primary_label', $setting->hero_primary_label) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Primary Button URL</label>
                        <input name="hero_primary_url" value="{{ old('hero_primary_url', $setting->hero_primary_url) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Secondary Button Label</label>
                        <input name="hero_secondary_label" value="{{ old('hero_secondary_label', $setting->hero_secondary_label) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Secondary Button URL</label>
                        <input name="hero_secondary_url" value="{{ old('hero_secondary_url', $setting->hero_secondary_url) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Call to Action</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Title</label>
                        <input name="cta_title" value="{{ old('cta_title', $setting->cta_title) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Subtitle</label>
                        <textarea name="cta_subtitle" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="2">{{ old('cta_subtitle', $setting->cta_subtitle) }}</textarea>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Primary CTA Label</label>
                        <input name="cta_primary_label" value="{{ old('cta_primary_label', $setting->cta_primary_label) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Primary CTA URL</label>
                        <input name="cta_primary_url" value="{{ old('cta_primary_url', $setting->cta_primary_url) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Secondary CTA Label</label>
                        <input name="cta_secondary_label" value="{{ old('cta_secondary_label', $setting->cta_secondary_label) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Secondary CTA URL</label>
                        <input name="cta_secondary_url" value="{{ old('cta_secondary_url', $setting->cta_secondary_url) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900">Contact</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Support Email</label>
                        <input name="contact_email" value="{{ old('contact_email', $setting->contact_email) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Support Phone</label>
                        <input name="contact_phone" value="{{ old('contact_phone', $setting->contact_phone) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
