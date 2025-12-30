@extends('admin.layouts.app')

@section('page_heading', 'Add Device')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('admin.devices.index') }}" class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-slate-500 transition-colors hover:text-slate-900">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
            </svg>
            Back to Devices
        </a>

       
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    Please review the highlighted fields.
                </div>
            @endif
            <form method="POST" action="{{ route('admin.devices.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label class="text-sm font-medium text-slate-900">Owner *</label>
                    <select
                        name="owner_id"
                        class="mt-2 h-12 w-full rounded-lg border bg-white px-4 text-sm {{ $errors->has('owner_id') ? 'border-rose-300 bg-rose-50/40 text-rose-700' : 'border-slate-200' }}"
                        required
                    >
                        <option value="" disabled @selected(old('owner_id') === null)>Select a user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('owner_id') == $user->id)>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('owner_id')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-900">IMEI Number *</label>
                        <input
                            name="imei"
                            value="{{ old('imei') }}"
                            placeholder="Enter 15-digit IMEI"
                            inputmode="numeric"
                            pattern="[0-9]{15}"
                            minlength="15"
                            maxlength="15"
                            class="mt-2 h-12 w-full rounded-lg border bg-white px-4 text-sm font-mono {{ $errors->has('imei') ? 'border-rose-300 bg-rose-50/40 text-rose-700 placeholder-rose-300' : 'border-slate-200' }}"
                            required
                        />
                        @error('imei')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-slate-500">Dial <span class="font-mono font-medium">*#06#</span> to find the IMEI.</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-slate-900">Status *</label>
                        <select
                            name="status"
                            class="mt-2 h-12 w-full rounded-lg border bg-white px-4 text-sm {{ $errors->has('status') ? 'border-rose-300 bg-rose-50/40 text-rose-700' : 'border-slate-200' }}"
                            required
                        >
                            <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                            <option value="transferred" @selected(old('status') === 'transferred')>Transferred</option>
                            <option value="lost" @selected(old('status') === 'lost')>Lost / Missing</option>
                            <option value="suspicious" @selected(old('status') === 'suspicious')>Suspicious</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-900">Brand *</label>
                        <select name="brand" class="mt-2 h-12 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm">
                            <option value="">Select brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand }}" @selected(old('brand') === $brand)>{{ $brand }}</option>
                            @endforeach
                        </select>
                        @error('brand')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-900">Model *</label>
                        <input
                            name="model"
                            value="{{ old('model') }}"
                            placeholder="e.g., iPhone 15 Pro"
                            class="mt-2 h-12 w-full rounded-lg border bg-white px-4 text-sm {{ $errors->has('model') ? 'border-rose-300 bg-rose-50/40 text-rose-700 placeholder-rose-300' : 'border-slate-200' }}"
                        />
                        @error('model')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-900">Device Type *</label>
                    <select name="device_type" class="mt-2 h-12 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm">
                        <option value="">Select device type</option>
                        @foreach ($deviceTypes as $type)
                            <option value="{{ $type['value'] }}" @selected(old('device_type') === $type['value'])>{{ $type['label'] }}</option>
                        @endforeach
                    </select>
                    @error('device_type')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-900">Purchase Type *</label>
                    <div class="mt-3 grid gap-4 sm:grid-cols-2">
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                            <input type="radio" name="purchase_type" value="new" class="h-4 w-4" @checked(old('purchase_type', 'new') === 'new') />
                            <div>
                                <p class="font-medium text-slate-900">Brand New</p>
                                <p class="text-xs text-slate-500">Purchased from authorized seller</p>
                            </div>
                        </label>
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                            <input type="radio" name="purchase_type" value="secondhand" class="h-4 w-4" @checked(old('purchase_type') === 'secondhand') />
                            <div>
                                <p class="font-medium text-slate-900">Second-hand</p>
                                <p class="text-xs text-slate-500">Ownership confirmation required</p>
                            </div>
                        </label>
                    </div>
                    @error('purchase_type')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-900">Purchase Date *</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="mt-2 h-12 w-full rounded-lg border border-slate-200 bg-white px-4 text-sm" />
                        @error('purchase_date')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-900">Invoice (optional)</label>
                        <input type="file" name="invoice" class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm" />
                        @error('invoice')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-900">Seller Name (optional)</label>
                    <input
                        name="seller_name"
                        value="{{ old('seller_name') }}"
                        placeholder="Retailer or seller"
                        class="mt-2 h-12 w-full rounded-lg border bg-white px-4 text-sm {{ $errors->has('seller_name') ? 'border-rose-300 bg-rose-50/40 text-rose-700 placeholder-rose-300' : 'border-slate-200' }}"
                    />
                    @error('seller_name')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[color:var(--brand-600)] px-6 py-3 text-sm font-semibold text-white shadow-sm hover:opacity-90">
                    Save Device
                </button>
            </form>
        </div>
    </div>
@endsection
