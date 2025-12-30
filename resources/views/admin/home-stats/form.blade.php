<div>
    <label class="text-sm font-medium text-slate-700">Label</label>
    <input name="label" value="{{ old('label', $stat->label ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
</div>
<div>
    <label class="text-sm font-medium text-slate-700">Value</label>
    <input name="value" value="{{ old('value', $stat->value ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $stat->sort_order ?? 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
    <label class="mt-8 flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $stat->is_active ?? true) ? 'checked' : '' }} />
        Active
    </label>
</div>
