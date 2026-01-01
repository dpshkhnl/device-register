@extends('admin.layouts.app')

@section('page_heading', 'Brands')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Brands</h1>
                <p class="text-sm text-slate-500">Manage device brand options.</p>
            </div>
            <a href="{{ route('admin.brands.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Add Brand</a>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Active</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($brands as $brand)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $brand->name }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $brand->sort_order }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $brand->is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.brands.edit', $brand) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs text-rose-600">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No brands yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
