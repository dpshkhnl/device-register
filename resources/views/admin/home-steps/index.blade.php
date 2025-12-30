@extends('admin.layouts.app')

@section('page_heading', 'Home Steps')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Homepage Steps</h1>
                <p class="text-sm text-slate-500">Manage the “How it Works” steps.</p>
            </div>
            <a href="{{ route('admin.home-steps.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Add Step</a>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="space-y-4">
            @foreach ($steps as $step)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $step->title }}</h3>
                            <p class="text-sm text-slate-500">{{ $step->description }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.home-steps.edit', $step) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.home-steps.destroy', $step) }}">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-sm text-rose-600">Delete</button>
                            </form>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-slate-400">Icon: {{ $step->icon }} • Order: {{ $step->sort_order }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
