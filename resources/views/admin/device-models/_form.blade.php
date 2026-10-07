<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Product</label>
        <select name="product_id" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
            <option value="">Select product</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected(old('product_id', $deviceModel?->product_id) == $product->id)>{{ $product->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">Brand</label>
        <select name="brand_id" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
            <option value="">Select brand</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $deviceModel?->brand_id) == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('brand_id')" class="mt-2" />
    </div>
</div>
<div>
    <label class="text-sm font-medium text-slate-700">Model Name</label>
    <input name="name" value="{{ old('name', $deviceModel?->name) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="iPhone 16 Pro" required>
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <label class="text-sm font-medium text-slate-700">Storage Options</label>
    <input name="storage_options" value="{{ old('storage_options', implode(', ', $deviceModel?->storage_options ?? [])) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="128GB, 256GB, 512GB, 1TB">
    <p class="mt-1 text-xs text-slate-400">Comma separated. Plain numbers are treated as GB. Leave blank if not applicable (e.g. watches).</p>
    <x-input-error :messages="$errors->get('storage_options')" class="mt-2" />
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Sort Order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $deviceModel?->sort_order ?? 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
    </div>
    <label class="mt-6 inline-flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" {{ old('is_active', $deviceModel?->is_active ?? true) ? 'checked' : '' }}>
        Active
    </label>
</div>
