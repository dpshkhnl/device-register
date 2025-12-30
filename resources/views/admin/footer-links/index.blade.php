@extends('admin.layouts.app')

@section('page_heading', 'Footer Links')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Footer Links</h1>
                <p class="text-sm text-slate-500">Manage footer navigation groups.</p>
            </div>
            <a href="{{ route('admin.footer-links.create') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Add Link</a>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="space-y-4">
            @foreach ($links as $link)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $link->group }} • {{ $link->label }}</h3>
                            <p class="text-sm text-slate-500">{{ $link->url }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.footer-links.edit', $link) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.footer-links.destroy', $link) }}">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-lg border border-rose-200 px-3 py-1.5 text-sm text-rose-600">Delete</button>
                            </form>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-slate-400">Order: {{ $link->sort_order }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
