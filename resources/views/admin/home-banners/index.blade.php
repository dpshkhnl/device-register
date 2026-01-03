@extends('admin.layouts.app')

@section('page_heading', 'Home Banners')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Homepage Banners</h1>
                <p class="text-sm text-slate-500">Manage the rotating banner slides.</p>
            </div>
            <a href="{{ route('admin.home-banners.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Add Banner</a>
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
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Link</th>
                        <th class="px-4 py-3">Image</th>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Active</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($banners as $banner)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $banner->title }}</p>
                                <p class="text-xs text-slate-500">{{ $banner->subtitle }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                {{ $banner->link_label ?? '-' }}<br />
                                <span class="text-slate-400">{{ $banner->link_url ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">
                                @if ($banner->image_url)
                                    <div class="flex items-center gap-2">
                                        <img src="{{ asset('storage/' . $banner->image_url) }}" alt="Banner image" class="h-8 w-8 rounded-lg object-cover" />
                                        <span class="text-slate-500">{{ $banner->image_url }}</span>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $banner->sort_order }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $banner->is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.home-banners.edit', $banner) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.home-banners.destroy', $banner) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs text-rose-600">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No banners yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
