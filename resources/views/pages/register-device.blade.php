@extends('layouts.app')

@section('content')
<section class="relative overflow-hidden bg-background py-12 lg:py-16">
    <div class="pointer-events-none absolute inset-0 hidden opacity-25 lg:block"
        style="background-image: url('/images/hero-device.svg'); background-repeat: no-repeat; background-position: right 6% top 12%; background-size: 220px;">
    </div>
    <div class="container max-w-6xl">
        <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" />
                </svg>
                Back to Dashboard
            </a>
            <div class="rounded-full border border-border bg-card px-4 py-1.5 text-xs font-semibold text-muted-foreground">Device Registration</div>
        </div>

        <div class="grid overflow-hidden rounded-3xl border border-border bg-card shadow-soft lg:grid-cols-2">
            <div class="relative hidden bg-muted/40 p-10 lg:block">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/15 via-transparent to-transparent"></div>
                <div class="relative z-10 flex h-full flex-col justify-between">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10">
                            <svg class="h-6 w-6 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="6" y="3" width="12" height="18" rx="2" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 17h4" />
                            </svg>
                        </div>
                        <h1 class="mt-6 text-2xl font-bold text-foreground">Register Device</h1>
                        <p class="mt-2 text-xs text-muted-foreground">Add a new device to your account for verification and protection.</p>
                    </div>
                    <div class="mt-10 space-y-4">
                        <div class="rounded-2xl border border-border bg-white/80 p-4">
                            <p class="text-xs font-semibold text-muted-foreground">Step 1</p>
                            <p class="mt-1 text-sm font-semibold text-foreground">IMEI & Device Details</p>
                        </div>
                        <div class="rounded-2xl border border-border bg-white/80 p-4">
                            <p class="text-xs font-semibold text-muted-foreground">Step 2</p>
                            <p class="mt-1 text-sm font-semibold text-foreground">Purchase Information</p>
                        </div>
                        <div class="rounded-2xl border border-border bg-white/80 p-4">
                            <p class="text-xs font-semibold text-muted-foreground">Step 3</p>
                            <p class="mt-1 text-sm font-semibold text-foreground">Upload Invoice</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 sm:p-10">
                <div class="space-y-2">
                    <h2 class="text-2xl font-semibold text-foreground">Device details</h2>
                    <p class="text-xs text-muted-foreground">Fill in the device and purchase information.</p>
                </div>

                @if ($packageError = $errors->first('package') ?: $packageError)
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        <span>{{ $packageError }}</span>
                        <a href="{{ route('packages.index') }}" class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700">View packages</a>
                    </div>
                @elseif ($errors->any())
                    <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        Please check the form for errors.
                    </div>
                @endif

                <form class="mt-6 space-y-5" method="POST" action="{{ route('devices.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-5"
                        x-data="{
                            catalog: @js($catalog),
                            product: @js(old('product', '')),
                            brand: @js(old('brand', '')),
                            model: @js(old('model', '')),
                            storage: @js(old('storage', '')),
                            get usesSerial() { return this.catalog.find(p => p.slug === this.product)?.identifier === 'serial'; },
                            get brands() { return this.catalog.find(p => p.slug === this.product)?.brands ?? []; },
                            get models() { return this.brands.find(b => b.name === this.brand)?.models ?? []; },
                            get storages() { return this.models.find(m => m.name === this.model)?.storage ?? []; },
                        }"
                    >
                    <div
                        class="grid gap-5 sm:grid-cols-2"
                    >
                        <div>
                            <label class="text-xs font-semibold text-foreground">Product <span class="text-rose-600">*</span></label>
                            <select name="product" x-model="product" @change="brand = ''; model = ''; storage = ''" class="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 text-sm disabled:cursor-not-allowed disabled:opacity-60">
                                <option value="">Select product</option>
                                <template x-for="p in catalog" :key="p.slug">
                                    <option :value="p.slug" x-text="p.name" :selected="p.slug === product"></option>
                                </template>
                            </select>
                            @error('product')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-foreground">Brand <span class="text-rose-600">*</span></label>
                            <select name="brand" x-model="brand" @change="model = ''; storage = ''" :disabled="!brands.length" class="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 text-sm disabled:cursor-not-allowed disabled:opacity-60">
                                <option value="">Select brand</option>
                                <template x-for="b in brands" :key="b.name">
                                    <option :value="b.name" x-text="b.name" :selected="b.name === brand"></option>
                                </template>
                            </select>
                            @error('brand')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-foreground">Model <span class="text-rose-600">*</span></label>
                            <select name="model" x-model="model" @change="storage = ''" :disabled="!models.length" class="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 text-sm disabled:cursor-not-allowed disabled:opacity-60">
                                <option value="">Select model</option>
                                <template x-for="m in models" :key="m.name">
                                    <option :value="m.name" x-text="m.name" :selected="m.name === model"></option>
                                </template>
                            </select>
                            @error('model')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-foreground">Storage (GB) <span x-show="storages.length" class="text-rose-600">*</span></label>
                            <select name="storage" x-model="storage" :disabled="!storages.length" class="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 text-sm disabled:cursor-not-allowed disabled:opacity-60">
                                <option value="" x-text="model && !storages.length ? 'Not applicable' : 'Select storage'"></option>
                                <template x-for="s in storages" :key="s">
                                    <option :value="s" x-text="s" :selected="s === storage"></option>
                                </template>
                            </select>
                            @error('storage')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div x-show="!usesSerial" class="space-y-5">
                    <div>
                        <label class="text-xs font-semibold text-foreground">IMEI Number <span class="text-rose-600">*</span></label>
                        <input
                            type="text"
                            name="imei"
                            placeholder="Enter 15-digit IMEI"
                            inputmode="numeric"
                            pattern="[0-9]{15}"
                            minlength="15"
                            maxlength="15"
                            :required="!usesSerial"
                            :disabled="usesSerial"
                            value="{{ old('imei') }}"
                            class="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 text-base font-mono focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        />
                        @error('imei')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-muted-foreground">Dial <span class="font-mono font-medium">*#06#</span> to find your IMEI.</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-foreground">IMEI 2 (Optional)</label>
                        <input
                            type="text"
                            name="imei2"
                            placeholder="Enter second 15-digit IMEI"
                            inputmode="numeric"
                            pattern="[0-9]{15}"
                            minlength="15"
                            maxlength="15"
                            :disabled="usesSerial"
                            value="{{ old('imei2') }}"
                            class="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 text-base font-mono focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        />
                        @error('imei2')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-muted-foreground">Leave blank if your device has only one IMEI.</p>
                    </div>
                    </div>

                    <div x-show="usesSerial" style="display: none">
                        <label class="text-xs font-semibold text-foreground">Serial Number <span class="text-rose-600">*</span></label>
                        <input
                            type="text"
                            name="imei"
                            placeholder="Enter device serial number"
                            autocapitalize="characters"
                            minlength="4"
                            maxlength="50"
                            :required="usesSerial"
                            :disabled="!usesSerial"
                            disabled
                            value="{{ old('imei') }}"
                            class="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 text-base font-mono uppercase focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        />
                        @error('imei')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-muted-foreground">Find it on the back of the device, the box, or in Settings → About.</p>
                    </div>

                    </div>

                    <div class="space-y-5" x-data="{ purchaseType: @js(old('purchase_type', 'new')) }">
                    <div>
                        <label class="text-xs font-semibold text-foreground">Purchase Type <span class="text-rose-600">*</span></label>
                        <div class="mt-3 grid gap-4 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-muted/30 p-4">
                                <input type="radio" name="purchase_type" value="new" x-model="purchaseType" class="h-4 w-4" @checked(old('purchase_type', 'new') === 'new') />
                                <div>
                                    <p class="text-sm font-semibold text-foreground">Brand New</p>
                                    <p class="text-xs text-muted-foreground">Purchased from authorized seller</p>
                                </div>
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-border bg-muted/30 p-4">
                                <input type="radio" name="purchase_type" value="secondhand" x-model="purchaseType" class="h-4 w-4" @checked(old('purchase_type') === 'secondhand') />
                                <div>
                                    <p class="text-sm font-semibold text-foreground">Second-hand</p>
                                    <p class="text-xs text-muted-foreground">Ownership confirmation required</p>
                                </div>
                            </label>
                        </div>
                        @error('purchase_type')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div x-show="purchaseType === 'new'" @style(['display: none' => old('purchase_type', 'new') !== 'new'])>
                            <label class="text-xs font-semibold text-foreground">Purchase Date <span class="text-rose-600">*</span></label>
                            <input
                                type="text"
                                name="purchase_date"
                                value="{{ old('purchase_date') }}"
                                placeholder="Select purchase date"
                                x-init="flatpickr($el, { maxDate: 'today', dateFormat: 'Y-m-d', altInput: true, altFormat: 'd M Y', disableMobile: true })"
                                :disabled="purchaseType !== 'new'"
                                class="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 text-sm"
                            />
                            <p class="mt-2 text-xs text-muted-foreground">Today or an earlier date.</p>
                            @error('purchase_date')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div x-show="purchaseType === 'secondhand'" @style(['display: none' => old('purchase_type', 'new') !== 'secondhand'])>
                            <label class="text-xs font-semibold text-foreground">How old is the device? <span class="text-rose-600">*</span></label>
                            <select name="device_age" :disabled="purchaseType !== 'secondhand'" class="mt-2 h-12 w-full rounded-xl border border-border bg-background px-4 text-sm">
                                <option value="">Select device age</option>
                                @foreach ($deviceAges as $age)
                                    <option value="{{ $age }}" @selected(old('device_age') === $age)>{{ $age }}</option>
                                @endforeach
                            </select>
                            @error('device_age')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2" x-data="{ preview: null, name: '', isImage: false }">
                            <label class="text-xs font-semibold text-foreground">Invoice (optional)</label>
                            <input
                                type="file"
                                name="invoice"
                                accept=".pdf,.jpg,.jpeg,.png,.webp"
                                x-ref="invoice"
                                @change="
                                    const file = $event.target.files[0];
                                    if (preview) URL.revokeObjectURL(preview);
                                    preview = file ? URL.createObjectURL(file) : null;
                                    name = file?.name ?? '';
                                    isImage = !!file && file.type.startsWith('image/');
                                "
                                class="mt-2 w-full rounded-xl border border-border bg-background px-4 py-2 text-sm"
                            />
                            <div x-show="preview" style="display: none" class="mt-3 overflow-hidden rounded-xl border border-border bg-muted/30">
                                <template x-if="isImage">
                                    <img :src="preview" alt="Invoice preview" class="max-h-80 w-full bg-white object-contain" />
                                </template>
                                <template x-if="!isImage">
                                    <iframe :src="preview" title="Invoice preview" class="h-80 w-full bg-white"></iframe>
                                </template>
                                <div class="flex items-center justify-between gap-3 border-t border-border px-4 py-2">
                                    <span class="truncate text-xs text-muted-foreground" x-text="name"></span>
                                    <button type="button" @click="URL.revokeObjectURL(preview); preview = null; name = ''; $refs.invoice.value = ''" class="shrink-0 text-xs font-semibold text-rose-600 hover:text-rose-700">Remove</button>
                                </div>
                            </div>
                            @error('invoice')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-soft hover:bg-primary/90">
                        Register Device
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<style>
    /* Future dates: clearly unavailable. */
    .flatpickr-day.flatpickr-disabled, .flatpickr-day.flatpickr-disabled:hover { color: #cbd5e1; text-decoration: line-through; }
    .flatpickr-day.selected, .flatpickr-day.selected:hover { background: #0e7c86; border-color: #0e7c86; }
</style>
@endsection
