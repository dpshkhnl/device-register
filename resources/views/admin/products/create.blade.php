@extends('admin.layouts.app')

@section('page_heading', 'Add Product')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Add Product</h1>
                <p class="text-sm text-slate-500">Create a new product type.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Back</a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-5">
                @csrf
                @include('admin.products._form', ['product' => null])
                <button class="rounded-lg bg-[color:var(--brand-600)] px-4 py-2 text-sm font-semibold text-white">Create Product</button>
            </form>
        </div>
    </div>
</div>
@endsection
