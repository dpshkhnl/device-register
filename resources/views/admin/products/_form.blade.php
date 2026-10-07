<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Product Name</label>
        <input name="name" value="{{ old('name', $product?->name) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Mobile Phone" required>
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">Slug</label>
        <input name="slug" value="{{ old('slug', $product?->slug) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="Auto from name, e.g. smartphone">
        <p class="mt-1 text-xs text-slate-400">Saved on each registered device. Avoid changing it once devices use it.</p>
        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
    </div>
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Sort Order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $product?->sort_order ?? 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
        <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
    </div>
    <label class="mt-6 inline-flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-[color:var(--brand-600)]" {{ old('is_active', $product?->is_active ?? true) ? 'checked' : '' }}>
        Active
    </label>
</div>
