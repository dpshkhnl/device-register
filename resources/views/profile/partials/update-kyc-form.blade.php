@php
    $kycBadge = match ($user->kyc_status) {
        \App\Models\User::KYC_APPROVED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        \App\Models\User::KYC_PENDING => 'bg-amber-50 text-amber-700 border-amber-200',
        \App\Models\User::KYC_REJECTED => 'bg-rose-50 text-rose-700 border-rose-200',
        default => 'bg-gray-50 text-gray-600 border-gray-200',
    };
    $kycErrors = $errors->kyc;
@endphp

<section>
    <header class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-medium text-gray-900">KYC Verification</h2>
            <p class="mt-1 text-sm text-gray-600">
                Required before you can transfer a device or report it lost.
            </p>
        </div>
        <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold {{ $kycBadge }}">
            {{ $user->kycStatusLabel() }}
        </span>
    </header>

    @if (session('kyc_required'))
        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ session('kyc_required') }}
        </div>
    @endif

    @if ($user->kyc_status === \App\Models\User::KYC_REJECTED && $user->kyc_rejection_reason)
        <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            Rejected: {{ $user->kyc_rejection_reason }}. Please correct and submit again.
        </div>
    @endif

    @if (session('status') === 'kyc-submitted')
        <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            KYC submitted. You can now transfer devices and report lost devices while we review it.
        </div>
    @endif

    <form method="post" action="{{ route('profile.kyc.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf

        <div>
            <x-input-label for="alternate_mobile" value="Alternate Mobile Number" />
            <x-text-input id="alternate_mobile" name="alternate_mobile" type="tel" inputmode="tel" class="mt-1 block w-full" :value="old('alternate_mobile', $user->alternate_mobile)" placeholder="98XXXXXXXX" required />
            <p class="mt-1 text-xs text-gray-500">A family member or second number we can reach you on. Must differ from {{ $user->mobile ?: 'your main number' }}.</p>
            <x-input-error class="mt-2" :messages="$kycErrors->get('alternate_mobile')" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="kyc_id_type" value="Government ID Type" />
                <select id="kyc_id_type" name="kyc_id_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Select ID type</option>
                    @foreach (\App\Models\User::KYC_ID_TYPES as $value => $label)
                        <option value="{{ $value }}" @selected(old('kyc_id_type', $user->kyc_id_type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$kycErrors->get('kyc_id_type')" />
            </div>
            <div>
                <x-input-label for="kyc_id_number" value="ID Number" />
                <x-text-input id="kyc_id_number" name="kyc_id_number" type="text" class="mt-1 block w-full" :value="old('kyc_id_number', $user->kyc_id_number)" required />
                <x-input-error class="mt-2" :messages="$kycErrors->get('kyc_id_number')" />
            </div>
        </div>

        @foreach ([
            'photo' => ['Your Photo', 'A clear, recent photo of your face. JPG/PNG, max 4 MB.', 'image/*', 'kyc_photo_path', true],
            'id_front' => ['ID Front', 'Front side of the ID. JPG/PNG/PDF, max 5 MB.', 'image/*,application/pdf', 'kyc_id_front_path', true],
            'id_back' => ['ID Back (optional)', 'Back side, if your ID has one.', 'image/*,application/pdf', 'kyc_id_back_path', false],
        ] as $field => [$label, $help, $accept, $column, $required])
            <div>
                <x-input-label for="{{ $field }}" :value="$label" />
                <div class="mt-1 flex items-center gap-3">
                    @if ($user->{$column})
                        <a href="{{ route('profile.kyc.file', $field) }}" target="_blank" class="shrink-0 text-xs font-semibold text-indigo-600 underline">View current</a>
                    @endif
                    <input id="{{ $field }}" name="{{ $field }}" type="file" accept="{{ $accept }}"
                        class="block w-full rounded-md border border-gray-300 text-sm file:mr-3 file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm"
                        @required($required && ! $user->{$column}) />
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $help }}{{ $user->{$column} ? ' Leave empty to keep the current file.' : '' }}</p>
                <x-input-error class="mt-2" :messages="$kycErrors->get($field)" />
            </div>
        @endforeach

        <div class="flex items-center gap-4">
            <x-primary-button>{{ $user->kyc_status === \App\Models\User::KYC_NOT_SUBMITTED ? 'Submit KYC' : 'Update KYC' }}</x-primary-button>
            @if ($user->kyc_status === \App\Models\User::KYC_APPROVED)
                <p class="text-xs text-gray-500">Updating will send your KYC for review again.</p>
            @endif
        </div>
    </form>
</section>
