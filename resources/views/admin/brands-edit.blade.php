@extends('admin.layouts.app')

@section('page_heading', 'Edit Brand')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Edit Brand</h1>
                <p class="text-sm text-slate-500">Update brand name and status.</p>
            </div>
            <a href="{{ route('admin.brands.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Back</a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.brands.update', $brand) }}" class="space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-medium text-slate-700">Brand Name</label>
                    <input name="name" value="{{ old('name', $brand->name) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Sort Order</label>
                        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $brand->sort_order) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                    </div>
                    <label class="mt-6 inline-flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" {{ old('is_active', $brand->is_active) ? 'checked' : '' }}>
                        Active
                    </label>
                </div>
                <button class="rounded-lg bg-[color:var(--brand-600)] px-4 py-2 text-sm font-semibold text-white">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
