@php
    $kycPill = match ($user->kyc_status) {
        \App\Models\User::KYC_APPROVED => 'pill-success',
        \App\Models\User::KYC_PENDING => 'pill-warning',
        \App\Models\User::KYC_REJECTED => 'pill-danger',
        default => 'pill-neutral',
    };
    $kycErrors = $errors->kyc;
@endphp

<section>
    <header class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-foreground">KYC verification</h2>
            <p class="mt-1 text-sm text-muted-foreground">Required before you can transfer a device or report it lost.</p>
        </div>
        <span class="pill {{ $kycPill }}">{{ $user->kycStatusLabel() }}</span>
    </header>

    @if (session('kyc_required'))
        <div class="callout callout-warning mt-5">
            <x-site.icon name="info" class="mt-px h-5 w-5 shrink-0" />
            <p>{{ session('kyc_required') }}</p>
        </div>
    @endif

    @if ($user->kyc_status === \App\Models\User::KYC_REJECTED && $user->kyc_rejection_reason)
        <div class="callout callout-danger mt-5">
            <x-site.icon name="x-circle" class="mt-px h-5 w-5 shrink-0" />
            <p><span class="font-semibold">Rejected:</span> {{ $user->kyc_rejection_reason }}. Please correct and submit again.</p>
        </div>
    @endif

    <form method="post" action="{{ route('profile.kyc.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf

        <div>
            <label class="field-label" for="alternate_mobile">Alternate mobile number <span class="req">*</span></label>
            <input id="alternate_mobile" name="alternate_mobile" type="tel" inputmode="tel" value="{{ old('alternate_mobile', $user->alternate_mobile) }}" placeholder="98XXXXXXXX" required class="field-input" />
            <p class="field-help">A family member or second number we can reach you on. Must differ from {{ $user->mobile ?: 'your main number' }}.</p>
            <x-site.error :messages="$kycErrors->get('alternate_mobile')" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="field-label" for="kyc_id_type">Government ID type <span class="req">*</span></label>
                <select id="kyc_id_type" name="kyc_id_type" required class="field-input">
                    <option value="">Select ID type</option>
                    @foreach (\App\Models\User::KYC_ID_TYPES as $value => $label)
                        <option value="{{ $value }}" @selected(old('kyc_id_type', $user->kyc_id_type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-site.error :messages="$kycErrors->get('kyc_id_type')" />
            </div>
            <div>
                <label class="field-label" for="kyc_id_number">ID number <span class="req">*</span></label>
                <input id="kyc_id_number" name="kyc_id_number" type="text" value="{{ old('kyc_id_number', $user->kyc_id_number) }}" required class="field-input" />
                <x-site.error :messages="$kycErrors->get('kyc_id_number')" />
            </div>
        </div>

        <div class="space-y-4">
            @foreach ([
                'photo' => ['Your photo', 'A clear, recent photo of your face. JPG/PNG, max 4 MB.', 'image/*', 'kyc_photo_path', true],
                'id_front' => ['ID — front', 'Front side of the ID. JPG/PNG/PDF, max 5 MB.', 'image/*,application/pdf', 'kyc_id_front_path', true],
                'id_back' => ['ID — back', 'Back side, if your ID has one.', 'image/*,application/pdf', 'kyc_id_back_path', false],
            ] as $field => [$label, $help, $accept, $column, $required])
                <div class="rounded-xl border border-border p-4">
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label class="text-[13px] font-semibold text-foreground" for="{{ $field }}">
                            {{ $label }}
                            @if ($required) <span class="text-destructive">*</span> @else <span class="font-normal text-muted-foreground">(optional)</span> @endif
                        </label>
                        @if ($user->{$column})
                            <a href="{{ route('profile.kyc.file', $field) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
                                <x-site.icon name="eye" class="h-3.5 w-3.5" /> View current
                            </a>
                        @endif
                    </div>
                    <input id="{{ $field }}" name="{{ $field }}" type="file" accept="{{ $accept }}" class="field-input" @required($required && ! $user->{$column}) />
                    <p class="field-help">{{ $help }}{{ $user->{$column} ? ' Leave empty to keep the current file.' : '' }}</p>
                    <x-site.error :messages="$kycErrors->get($field)" />
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center gap-4 pt-2">
            <button type="submit" class="btn btn-primary">{{ $user->kyc_status === \App\Models\User::KYC_NOT_SUBMITTED ? 'Submit KYC' : 'Update KYC' }}</button>
            @if ($user->kyc_status === \App\Models\User::KYC_APPROVED)
                <p class="text-xs text-muted-foreground">Updating will send your KYC for review again.</p>
            @endif
        </div>
    </form>
</section>
