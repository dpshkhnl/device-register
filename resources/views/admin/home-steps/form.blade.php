<div>
    <label class="text-sm font-medium text-slate-700">Title</label>
    <input name="title" value="{{ old('title', $step->title ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
</div>
<div>
    <label class="text-sm font-medium text-slate-700">Description</label>
    <textarea name="description" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="2">{{ old('description', $step->description ?? '') }}</textarea>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Icon Key</label>
        <input name="icon" value="{{ old('icon', $step->icon ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $step->sort_order ?? 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
</div>
<label class="flex items-center gap-2 text-sm text-slate-600">
    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $step->is_active ?? true) ? 'checked' : '' }} />
    Active
</label>
