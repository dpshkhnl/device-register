@extends('admin.layouts.app')

@section('page_heading', 'Reports')

@section('content')
    <div class="w-full space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Reports Generated</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $reportStats['generated'] }}</p>
                <x-ui.badge class="mt-3" status="active" label="This month" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Scheduled Exports</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $reportStats['scheduled'] }}</p>
                <x-ui.badge class="mt-3" status="transferred" label="Automated" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Authority Shares</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $reportStats['shared'] }}</p>
                <x-ui.badge class="mt-3" status="active" label="Approved" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Outstanding Reviews</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $reportStats['pending'] }}</p>
                <x-ui.badge class="mt-3" status="suspicious" label="Due" />
            </x-ui.card>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            @foreach ($reportCards as $card)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">{{ $card['title'] }}</h2>
                    <p class="mt-2 text-sm text-slate-500">{{ $card['description'] }}</p>
                    <button class="mt-4 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">
                        {{ $card['action'] }}
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endsection
