<div>
    <label class="text-sm font-medium text-slate-700">Title</label>
    <input name="title" value="{{ old('title', $feature->title ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
</div>
<div>
    <label class="text-sm font-medium text-slate-700">Description</label>
    <textarea name="description" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="2">{{ old('description', $feature->description ?? '') }}</textarea>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Icon Key</label>
        <input name="icon" value="{{ old('icon', $feature->icon ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $feature->sort_order ?? 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Button Label</label>
        <input name="button_label" value="{{ old('button_label', $feature->button_label ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">Button URL</label>
        <input name="button_url" value="{{ old('button_url', $feature->button_url ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
</div>
<label class="flex items-center gap-2 text-sm text-slate-600">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $feature->is_active ?? true) ? 'checked' : '' }} />
    Active
</label>
