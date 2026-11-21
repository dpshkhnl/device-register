{{-- Shows a thumbnail under any file input once an image is chosen. Add data-no-preview to opt out. --}}
<script>
    document.addEventListener('change', (event) => {
        const input = event.target;
        if (!(input instanceof HTMLInputElement) || input.type !== 'file' || input.hasAttribute('data-no-preview')) {
            return;
        }

        // Inputs sharing a flex row with other controls get the preview below the row.
        const anchor = input.parentElement?.classList.contains('flex') ? input.parentElement : input;
        let preview = anchor.nextElementSibling?.matches('[data-image-preview]') ? anchor.nextElementSibling : null;
        const file = input.files?.[0];

        if (preview) {
            URL.revokeObjectURL(preview.querySelector('img').src);
        }
        if (!file || !file.type.startsWith('image/')) {
            preview?.remove();
            return;
        }
        if (!preview) {
            preview = document.createElement('div');
            preview.dataset.imagePreview = '';
            preview.className = 'mt-3 inline-block overflow-hidden rounded-xl border border-gray-200 bg-gray-50 p-1';
            preview.innerHTML = '<img alt="Selected image preview" class="block max-h-48 max-w-full rounded-lg object-contain">';
            anchor.after(preview);
        }
        preview.querySelector('img').src = URL.createObjectURL(file);
    });
</script>
