@extends('admin.layouts.app')

@section('page_heading', 'Home Features')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-slate-900">Edit Feature</h1>
        <form method="POST" action="{{ route('admin.home-features.update', $feature) }}" class="mt-6 space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            @include('admin.home-features.form', ['feature' => $feature])
            <div class="flex justify-end">
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
