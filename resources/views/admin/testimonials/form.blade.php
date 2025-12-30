<div>
    <label class="text-sm font-medium text-slate-700">Name</label>
    <input name="name" value="{{ old('name', $testimonial->name ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
</div>
<div>
    <label class="text-sm font-medium text-slate-700">Role / Title</label>
    <input name="role" value="{{ old('role', $testimonial->role ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
</div>
<div>
    <label class="text-sm font-medium text-slate-700">Quote</label>
    <textarea name="quote" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" rows="3">{{ old('quote', $testimonial->quote ?? '') }}</textarea>
</div>
<div>
    <label class="text-sm font-medium text-slate-700">Avatar URL</label>
    <input name="avatar_url" value="{{ old('avatar_url', $testimonial->avatar_url ?? '') }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
</div>
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-slate-700">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order ?? 0) }}" class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
    </div>
    <label class="mt-8 flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }} />
        Active
    </label>
</div>
