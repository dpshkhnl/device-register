@extends('admin.layouts.app')

@section('page_heading', 'Packages')

@section('content')
    <div class="w-full space-y-6">

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Package list</h3>
                    <p class="text-sm text-slate-500">Pricing, limits, and availability.</p>
                </div>
                <a href="{{ route('admin.packages.create') }}" class="rounded-lg bg-[color:var(--brand-600)] px-3 py-2 text-xs font-semibold text-white">New Package</a>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">SN</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Devices</th>
                        <th class="px-6 py-4">IMEI Checks</th>
                        <th class="px-6 py-4">Trial</th>
                        <th class="px-6 py-4">Active</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($packages as $package)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $packages->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ $package->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ number_format($package->price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $package->device_limit }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $package->imei_limit }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                    {{ $package->is_trial ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full border border-slate-200 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                    {{ $package->is_active ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs text-slate-500">
                                    <a href="{{ route('admin.packages.edit', $package) }}" class="font-semibold text-slate-600 hover:text-slate-800">Edit</a>
                                    <span class="text-slate-300">|</span>
                                    <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" class="inline" data-swal-confirm data-swal-title="Delete package?" data-swal-text="This cannot be undone." data-swal-confirm="Delete">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-slate-500">No packages created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $packages->links() }}</div>
    </div>
@endsection
