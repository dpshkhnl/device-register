@extends('layouts.app')

@section('title', 'Register a device')

@section('content')
@php
    $packageError = $errors->first('package') ?: $packageError;
@endphp

<div class="container max-w-5xl py-10 lg:py-12">
    <a href="{{ route('dashboard') }}" class="btn btn-ghost btn-sm -ml-3 mb-4">
        <x-site.icon name="arrow-left" class="h-4 w-4" /> Dashboard
    </a>

    <div class="mb-8">
        <p class="eyebrow">New registration</p>
        <h1 class="page-title mt-2">Register a device</h1>
        <p class="page-lead">Put your device on record under your name. It takes about a minute — fields marked <span class="text-destructive">*</span> are required.</p>
    </div>

    @if ($packageError)
        <div class="callout callout-warning mb-6" role="alert">
            <x-site.icon name="info" class="mt-px h-5 w-5 shrink-0" />
            <div class="flex flex-1 flex-wrap items-center justify-between gap-3">
                <p><span class="font-semibold">{{ $packageError }}</span> Pick a plan, then come back here.</p>
                <a href="{{ route('packages.index') }}" class="btn btn-sm bg-amber-600 text-white hover:bg-amber-700">See plans</a>
            </div>
        </div>
    @elseif ($errors->any())
        <div class="callout callout-danger mb-6" role="alert">
            <x-site.icon name="alert" class="mt-px h-5 w-5 shrink-0" />
            <p>Some details need another look — see the highlighted fields below.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('devices.store') }}" enctype="multipart/form-data"
          class="grid gap-8 lg:grid-cols-[14rem,1fr]"
          x-data="{
              catalog: @js($catalog),
              product: @js(old('product', '')),
              brand: @js(old('brand', '')),
              model: @js(old('model', '')),
              storage: @js(old('storage', '')),
              purchaseType: @js(old('purchase_type', 'new')),
              idValue: @js(old('imei', '')),
              purchaseFilled: @js((bool) (old('purchase_date') || old('device_age'))),
              invoiceName: '',
              get usesSerial() { return this.catalog.find(p => p.slug === this.product)?.identifier === 'serial'; },
              get brands() { return this.catalog.find(p => p.slug === this.product)?.brands ?? []; },
              get models() { return this.brands.find(b => b.name === this.brand)?.models ?? []; },
              get storages() { return this.models.find(m => m.name === this.model)?.storage ?? []; },
              get deviceDone() { return !!(this.product && this.brand && this.model && (!this.storages.length || this.storage)); },
              get idDone() { return this.usesSerial ? this.idValue.trim().length >= 4 : /^[0-9]{15}$/.test(this.idValue); },
          }">
        @csrf

        {{-- Progress rail --}}
        <aside class="hidden lg:block">
            <ol class="sticky top-24 space-y-1">
                @foreach ([
                    ['n' => 1, 'label' => 'Device', 'done' => 'deviceDone', 'href' => '#sec-device'],
                    ['n' => 2, 'label' => 'IMEI / serial', 'done' => 'idDone', 'href' => '#sec-id'],
                    ['n' => 3, 'label' => 'Purchase', 'done' => 'purchaseFilled', 'href' => '#sec-purchase'],
                    ['n' => 4, 'label' => 'Invoice', 'done' => '!!invoiceName', 'href' => '#sec-invoice', 'optional' => true],
                ] as $s)
                    <li>
                        <a href="{{ $s['href'] }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-muted-foreground transition hover:bg-card hover:text-foreground">
                            <span class="step-dot h-7 w-7" :data-state="{{ $s['done'] }} ? 'done' : 'idle'">
                                <span x-show="!({{ $s['done'] }})">{{ $s['n'] }}</span>
                                <x-site.icon name="check" class="h-3.5 w-3.5" stroke="2.6" x-show="{{ $s['done'] }}" x-cloak />
                            </span>
                            {{ $s['label'] }}
                            @if (! empty($s['optional']))<span class="text-[11px] font-normal text-muted-foreground/70">optional</span>@endif
                        </a>
                    </li>
                @endforeach
            </ol>
        </aside>

        <div class="space-y-6">
            {{-- 1. Device --}}
            <section id="sec-device" class="surface scroll-mt-24 p-6 sm:p-8">
                <h2 class="flex items-center gap-3 text-lg font-bold text-foreground">
                    <span class="step-dot h-7 w-7 lg:hidden">1</span> Which device is it?
                </h2>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="field-label" for="product">Product <span class="req">*</span></label>
                        <select id="product" name="product" x-model="product" @change="brand = ''; model = ''; storage = ''" class="field-input">
                            <option value="">Select product</option>
                            <template x-for="p in catalog" :key="p.slug">
                                <option :value="p.slug" x-text="p.name" :selected="p.slug === product"></option>
                            </template>
                        </select>
                        @error('product') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label" for="brand">Brand <span class="req">*</span></label>
                        <select id="brand" name="brand" x-model="brand" @change="model = ''; storage = ''" :disabled="!brands.length" class="field-input">
                            <option value="" x-text="product ? 'Select brand' : 'Choose a product first'">Select brand</option>
                            <template x-for="b in brands" :key="b.name">
                                <option :value="b.name" x-text="b.name" :selected="b.name === brand"></option>
                            </template>
                        </select>
                        @error('brand') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label" for="model">Model <span class="req">*</span></label>
                        <select id="model" name="model" x-model="model" @change="storage = ''" :disabled="!models.length" class="field-input">
                            <option value="" x-text="brand ? 'Select model' : 'Choose a brand first'">Select model</option>
                            <template x-for="m in models" :key="m.name">
                                <option :value="m.name" x-text="m.name" :selected="m.name === model"></option>
                            </template>
                        </select>
                        @error('model') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label" for="storage">Storage <span x-show="storages.length" class="req">*</span></label>
                        <select id="storage" name="storage" x-model="storage" :disabled="!storages.length" class="field-input">
                            <option value="" x-text="model && !storages.length ? 'Not applicable' : 'Select storage'"></option>
                            <template x-for="s in storages" :key="s">
                                <option :value="s" x-text="s" :selected="s === storage"></option>
                            </template>
                        </select>
                        @error('storage') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- 2. Identifier --}}
            <section id="sec-id" class="surface scroll-mt-24 p-6 sm:p-8">
                <h2 class="flex items-center gap-3 text-lg font-bold text-foreground">
                    <span class="step-dot h-7 w-7 lg:hidden">2</span>
                    <span x-text="usesSerial ? 'Serial number' : 'IMEI number'">IMEI number</span>
                </h2>

                <div x-show="!usesSerial" class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="field-label" for="imei">IMEI <span class="req">*</span></label>
                        <input id="imei" type="text" name="imei" placeholder="15 digits" inputmode="numeric"
                               pattern="[0-9]{15}" minlength="15" maxlength="15"
                               :required="!usesSerial" :disabled="usesSerial"
                               value="{{ old('imei') }}" @input="idValue = $event.target.value"
                               class="field-input font-mono tracking-wider" />
                        @error('imei') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="field-label" for="imei2">Second IMEI <span class="font-normal text-muted-foreground">(dual-SIM)</span></label>
                        <input id="imei2" type="text" name="imei2" placeholder="Optional" inputmode="numeric"
                               pattern="[0-9]{15}" minlength="15" maxlength="15" :disabled="usesSerial"
                               value="{{ old('imei2') }}" class="field-input font-mono tracking-wider" />
                        @error('imei2') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="callout callout-info sm:col-span-2">
                        <x-site.icon name="info" class="mt-px h-5 w-5 shrink-0" />
                        <p>Dial <span class="font-mono font-semibold">*#06#</span> on the phone to see its IMEI. Dual-SIM phones show two — add both.</p>
                    </div>
                </div>

                <div x-show="usesSerial" style="display: none" class="mt-6">
                    <label class="field-label" for="serial">Serial number <span class="req">*</span></label>
                    <input id="serial" type="text" name="imei" placeholder="e.g. C02XK1ABJG5H" autocapitalize="characters"
                           minlength="4" maxlength="50" :required="usesSerial" :disabled="!usesSerial" disabled
                           value="{{ old('imei') }}" @input="idValue = $event.target.value"
                           class="field-input font-mono uppercase tracking-wider" />
                    @error('imei') <p class="field-error">{{ $message }}</p> @enderror
                    <p class="field-help">Find it on the back of the device, on the box, or in Settings → About.</p>
                </div>
            </section>

            {{-- 3. Purchase --}}
            <section id="sec-purchase" class="surface scroll-mt-24 p-6 sm:p-8">
                <h2 class="flex items-center gap-3 text-lg font-bold text-foreground">
                    <span class="step-dot h-7 w-7 lg:hidden">3</span> How did you get it?
                </h2>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <label class="choice">
                        <input type="radio" name="purchase_type" value="new" x-model="purchaseType" class="sr-only" @checked(old('purchase_type', 'new') === 'new') />
                        <span class="icon-tile h-10 w-10 bg-emerald-50 text-emerald-700"><x-site.icon name="sparkle" class="h-5 w-5" /></span>
                        <span class="flex-1">
                            <span class="block text-sm font-semibold text-foreground">Bought new</span>
                            <span class="block text-xs text-muted-foreground">From a shop or authorised seller</span>
                        </span>
                        <span class="choice-dot"><x-site.icon name="check" class="h-3 w-3" stroke="3" /></span>
                    </label>
                    <label class="choice">
                        <input type="radio" name="purchase_type" value="secondhand" x-model="purchaseType" class="sr-only" @checked(old('purchase_type') === 'secondhand') />
                        <span class="icon-tile h-10 w-10 bg-amber-50 text-amber-700"><x-site.icon name="transfer" class="h-5 w-5" /></span>
                        <span class="flex-1">
                            <span class="block text-sm font-semibold text-foreground">Second-hand</span>
                            <span class="block text-xs text-muted-foreground">From a previous owner</span>
                        </span>
                        <span class="choice-dot"><x-site.icon name="check" class="h-3 w-3" stroke="3" /></span>
                    </label>
                </div>
                @error('purchase_type') <p class="field-error">{{ $message }}</p> @enderror

                <div class="mt-5 max-w-sm">
                    <div x-show="purchaseType === 'new'" @style(['display: none' => old('purchase_type', 'new') !== 'new'])>
                        <label class="field-label" for="purchase_date">Purchase date <span class="req">*</span></label>
                        <input id="purchase_date" type="text" name="purchase_date" value="{{ old('purchase_date') }}" placeholder="Select a date"
                               x-init="flatpickr($el, { maxDate: 'today', dateFormat: 'Y-m-d', altInput: true, altFormat: 'd M Y', disableMobile: true, altInputClass: 'field-input', onChange: (d) => purchaseFilled = d.length > 0 })"
                               :disabled="purchaseType !== 'new'" class="field-input" />
                        <p class="field-help">Today or earlier.</p>
                        @error('purchase_date') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div x-show="purchaseType === 'secondhand'" @style(['display: none' => old('purchase_type', 'new') !== 'secondhand'])>
                        <label class="field-label" for="device_age">How old is the device? <span class="req">*</span></label>
                        <select id="device_age" name="device_age" :disabled="purchaseType !== 'secondhand'" @change="purchaseFilled = !!$event.target.value" class="field-input">
                            <option value="">Select age</option>
                            @foreach ($deviceAges as $age)
                                <option value="{{ $age }}" @selected(old('device_age') === $age)>{{ $age }}</option>
                            @endforeach
                        </select>
                        @error('device_age') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            {{-- 4. Invoice --}}
            <section id="sec-invoice" class="surface scroll-mt-24 p-6 sm:p-8" x-data="{ preview: null, isImage: false }">
                <h2 class="flex items-center gap-3 text-lg font-bold text-foreground">
                    <span class="step-dot h-7 w-7 lg:hidden">4</span> Invoice or receipt
                    <span class="pill pill-neutral pill-plain">Optional</span>
                </h2>
                <p class="mt-1 text-sm text-muted-foreground">Helps prove ownership if there’s ever a dispute.</p>

                <label x-show="!preview" class="mt-5 flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-input bg-muted/40 px-6 py-10 text-center transition hover:border-primary/50 hover:bg-accent/40">
                    <span class="icon-tile bg-card text-primary ring-1 ring-border"><x-site.icon name="upload" /></span>
                    <span class="text-sm font-semibold text-foreground">Click to upload</span>
                    <span class="text-xs text-muted-foreground">PDF, JPG, PNG or WEBP · up to 5 MB</span>
                    <input type="file" name="invoice" data-no-preview accept=".pdf,.jpg,.jpeg,.png,.webp" x-ref="invoice" class="sr-only"
                           @change="
                               const file = $event.target.files[0];
                               if (preview) URL.revokeObjectURL(preview);
                               preview = file ? URL.createObjectURL(file) : null;
                               invoiceName = file?.name ?? '';
                               isImage = !!file && file.type.startsWith('image/');
                           " />
                </label>
                <div x-show="preview" style="display: none" class="mt-5 overflow-hidden rounded-2xl border border-border">
                    <template x-if="isImage">
                        <img :src="preview" alt="Invoice preview" class="max-h-80 w-full bg-muted/40 object-contain" />
                    </template>
                    <template x-if="!isImage">
                        <iframe :src="preview" title="Invoice preview" class="h-80 w-full bg-white"></iframe>
                    </template>
                    <div class="flex items-center justify-between gap-3 border-t border-border bg-card px-4 py-3">
                        <span class="flex min-w-0 items-center gap-2 text-sm text-foreground">
                            <x-site.icon name="document" class="h-4 w-4 shrink-0 text-muted-foreground" />
                            <span class="truncate" x-text="invoiceName"></span>
                        </span>
                        <button type="button" @click="URL.revokeObjectURL(preview); preview = null; invoiceName = ''; $refs.invoice.value = ''"
                                class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700">Remove</button>
                    </div>
                </div>
                @error('invoice') <p class="field-error">{{ $message }}</p> @enderror
            </section>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('dashboard') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary btn-lg" @disabled($packageError)>
                    <x-site.icon name="shield-check" class="h-5 w-5" /> Register device
                </button>
            </div>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<style>
    .flatpickr-calendar { border-radius: 1rem; box-shadow: 0 20px 40px -16px rgba(20,22,40,.3); border: 1px solid #e3e0d9; font-family: inherit; }
    .flatpickr-day.flatpickr-disabled, .flatpickr-day.flatpickr-disabled:hover { color: #cbd5e1; text-decoration: line-through; }
    .flatpickr-day.selected, .flatpickr-day.selected:hover { background: #2b3cab; border-color: #2b3cab; }
    .flatpickr-day.today { border-color: #2b3cab55; }
</style>
@endsection
