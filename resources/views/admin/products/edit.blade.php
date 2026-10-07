@extends('admin.layouts.app')

@section('page_heading', 'Edit Product')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">Edit Product</h1>
                <p class="text-sm text-slate-500">Update product name and status.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Back</a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('admin.products.update', $product) }}" class="space-y-5">
                @csrf
                @method('PUT')
                @include('admin.products._form', ['product' => $product])
                <button class="rounded-lg bg-[color:var(--brand-600)] px-4 py-2 text-sm font-semibold text-white">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
