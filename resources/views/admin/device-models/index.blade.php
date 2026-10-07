@extends('admin.layouts.app')

@section('page_heading', 'Device Models')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Device Models</h1>
                <p class="text-sm text-slate-500">Models and storage options shown on the device registration form.</p>
            </div>
            <a href="{{ route('admin.device-models.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Add Model</a>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <form method="GET" class="mb-4 grid gap-3 sm:grid-cols-4">
            <select name="product_id" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                <option value="">All products</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
            <select name="brand_id" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
                <option value="">All brands</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>{{ $brand->name }}</option>
                @endforeach
            </select>
            <input name="search" value="{{ request('search') }}" placeholder="Search model" class="rounded-lg border border-slate-200 px-3 py-2 text-sm">
            <button class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Filter</button>
        </form>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Model</th>
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3">Brand</th>
                        <th class="px-4 py-3">Storage</th>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Active</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($deviceModels as $deviceModel)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $deviceModel->name }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $deviceModel->product?->name }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $deviceModel->brand?->name }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ implode(', ', $deviceModel->storage_options ?? []) ?: '—' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $deviceModel->sort_order }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $deviceModel->is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.device-models.edit', $deviceModel) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.device-models.destroy', $deviceModel) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs text-rose-600">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">No models found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $deviceModels->links() }}
        </div>
    </div>
</div>
@endsection
