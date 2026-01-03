@extends('admin.layouts.app')

@section('page_heading', 'Packages')

@section('content')
    <div class="w-full space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Packages</h2>
                <p class="text-sm text-slate-500">Manage purchase limits and trial access.</p>
            </div>
            <a href="{{ route('admin.packages.create') }}" class="rounded-lg bg-[color:var(--brand-600)] px-3 py-2 text-xs font-semibold text-white">New Package</a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Price</th>
                        <th class="px-4 py-3">Devices</th>
                        <th class="px-4 py-3">IMEI Checks</th>
                        <th class="px-4 py-3">Trial</th>
                        <th class="px-4 py-3">Active</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($packages as $package)
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-slate-900">{{ $package->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ number_format($package->price, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $package->device_limit }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $package->imei_limit }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $package->is_trial ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $package->is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.packages.edit', $package) }}" class="text-xs font-semibold text-[color:var(--brand-600)]">Edit</a>
                                    <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" data-swal-confirm data-swal-title="Delete package?" data-swal-text="This cannot be undone." data-swal-confirm="Delete">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs font-semibold text-rose-600">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">No packages created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>{{ $packages->links() }}</div>
    </div>
@endsection
