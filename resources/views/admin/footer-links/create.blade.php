@extends('admin.layouts.app')

@section('page_heading', 'Footer Links')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-slate-900">Add Footer Link</h1>
        <form method="POST" action="{{ route('admin.footer-links.store') }}" class="mt-6 space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @include('admin.footer-links.form', ['link' => null])
            <div class="flex justify-end">
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
