@extends('admin.layouts.app')

@section('page_heading', 'Home Banners')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.home-banners.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Back</a>
            <h1 class="text-2xl font-semibold text-slate-900">Edit Banner</h1>
        </div>

        <form method="POST" action="{{ route('admin.home-banners.update', $banner) }}" enctype="multipart/form-data" class="mt-6 space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            @include('admin.home-banners.form', ['banner' => $banner])

            <div class="flex justify-end">
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
