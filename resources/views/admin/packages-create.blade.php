@extends('admin.layouts.app')

@section('page_heading', 'Create Package')

@section('content')
    <div class="w-full space-y-6">
        <form method="POST" action="{{ route('admin.packages.store') }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-slate-700">Name</label>
                    <input name="name" value="{{ old('name') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Price</label>
                    <input name="price" type="number" step="0.01" min="0" value="{{ old('price', 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Device Limit</label>
                    <input name="device_limit" type="number" min="0" value="{{ old('device_limit', 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">IMEI Checks</label>
                    <input name="imei_limit" type="number" min="0" value="{{ old('imei_limit', 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Duration (days)</label>
                    <input name="duration_days" type="number" min="1" value="{{ old('duration_days') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                </div>
            </div>
            <div>
                <label class="text-sm font-medium text-slate-700">Description</label>
                <textarea name="description" rows="3" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('description') }}</textarea>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="is_trial" value="1" class="rounded border-slate-300" />
                    Trial Package
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" checked />
                    Active
                </label>
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.packages.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Cancel</a>
                <button class="rounded-lg bg-[color:var(--brand-600)] px-4 py-2 text-sm font-semibold text-white">Create Package</button>
            </div>
        </form>
    </div>
@endsection
