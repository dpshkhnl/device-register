<div>
    <label class="text-sm font-medium text-slate-700">Title</label>
    <input name="title" value="{{ old('title', $banner->title ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
</div>
<div>
    <label class="text-sm font-medium text-slate-700">Subtitle</label>
    <textarea name="subtitle" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="2">{{ old('subtitle', $banner->subtitle ?? '') }}</textarea>
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Link Label</label>
        <input name="link_label" value="{{ old('link_label', $banner->link_label ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
    <div>
        <label class="text-sm font-medium text-slate-700">Link URL</label>
        <input name="link_url" value="{{ old('link_url', $banner->link_url ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
</div>
<div>
    <label class="text-sm font-medium text-slate-700">Banner Image</label>
    <input type="file" name="image" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" accept="image/*" />
    @if (!empty($banner->image_url))
        <p class="mt-2 text-xs text-slate-500">Current: {{ $banner->image_url }}</p>
    @endif
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
    <label class="mt-8 flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }} />
        Active
    </label>
</div>
