@extends('admin.layouts.app')

@section('page_heading', 'Bulk Import')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('admin.devices.index') }}" class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition-colors hover:text-slate-900">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
            </svg>
            Back to Devices
        </a>

        <div class="mb-10 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[color:var(--brand-100)]">
                <svg class="h-7 w-7 text-[color:var(--brand-600)]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 17v2h16v-2M7 7l5-4 5 4M12 3v10" />
                </svg>
            </div>
            <h1 class="mb-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Bulk device upload</h1>
            <p class="text-lg text-slate-500">Import multiple devices and auto-create owners from an Excel file.</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @if (session('import_summary'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    Imported {{ session('import_summary.success') }} devices.
                    @if (session('import_summary.failed'))
                        {{ session('import_summary.failed') }} rows failed.
                    @endif
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    Please check the file and try again.
                </div>
            @endif

            <div class="space-y-4 text-sm text-slate-600">
                <p class="font-semibold text-slate-900">Required columns</p>
                <p>owner_name, owner_email, imei, brand, model, device_type, purchase_type, purchase_date, status</p>
                <p>Optional columns: owner_mobile, seller_name</p>
                <p>Purchase type must be <span class="font-mono">new</span> or <span class="font-mono">secondhand</span>. Status can be active, transferred, lost, suspicious.</p>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.devices.import.template') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:border-slate-300 hover:text-slate-800">
                    Download template
                </a>
            </div>

            <form method="POST" action="{{ route('admin.devices.import.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-slate-900">Upload Excel file *</label>
                    <input
                        type="file"
                        name="import_file"
                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm"
                        accept=".xlsx,.xls"
                        required
                    />
                    @error('import_file')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[color:var(--brand-600)] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:opacity-90">
                    Import Devices
                </button>
            </form>

            @if (session('import_errors'))
                <div class="mt-8 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    <p class="font-semibold text-slate-900">Rows with issues</p>
                    <div class="mt-3 space-y-2 text-xs">
                        @foreach (session('import_errors') as $error)
                            <div class="rounded-lg border border-slate-200 bg-white p-3">
                                <p class="font-semibold text-slate-800">Row {{ $error['row'] }}</p>
                                <ul class="mt-2 list-disc pl-4 text-slate-600">
                                    @foreach ($error['errors'] as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
