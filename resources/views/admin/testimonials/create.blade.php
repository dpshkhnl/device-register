@extends('admin.layouts.app')

@section('page_heading', 'Testimonials')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.testimonials.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Back</a>
            <h1 class="text-2xl font-semibold text-slate-900">Add Testimonial</h1>
        </div>

        <form method="POST" action="{{ route('admin.testimonials.store') }}" class="mt-6 space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @include('admin.testimonials.form', ['testimonial' => null])

            <div class="flex justify-end">
                <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
