@extends('admin.layouts.app')

@section('page_heading', 'Home Stats')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Homepage Stats</h1>
                <p class="text-sm text-slate-500">Manage the platform statistics shown on the homepage.</p>
            </div>
            <a href="{{ route('admin.home-stats.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Add Stat</a>
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
                        <th class="px-4 py-3">Label</th>
                        <th class="px-4 py-3">Value</th>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Active</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($stats as $stat)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $stat->label }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $stat->value }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $stat->sort_order }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500">{{ $stat->is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.home-stats.edit', $stat) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.home-stats.destroy', $stat) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs text-rose-600">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
