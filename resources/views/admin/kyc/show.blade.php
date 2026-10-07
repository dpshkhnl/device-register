@extends('admin.layouts.app')

@section('page_heading', 'Review KYC')

@section('content')
<div class="py-8">
    <div class="w-full sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">{{ $user->name }}</h1>
                <p class="text-sm text-slate-500">KYC status: <span class="font-semibold">{{ $user->kycStatusLabel() }}</span></p>
            </div>
            <a href="{{ route('admin.kyc.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600">Back</a>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @foreach ([
                    'Mobile' => $user->mobile,
                    'Alternate mobile' => $user->alternate_mobile,
                    'Email' => $user->email,
                    'ID type' => \App\Models\User::KYC_ID_TYPES[$user->kyc_id_type] ?? null,
                    'ID number' => $user->kyc_id_number,
                    'Submitted' => $user->kyc_submitted_at?->format('M d, Y H:i'),
                    'Reviewed' => $user->kyc_reviewed_at ? $user->kyc_reviewed_at->format('M d, Y H:i').' by '.($user->kycReviewer?->name ?? 'admin') : null,
                    'Rejection reason' => $user->kyc_rejection_reason,
                ] as $label => $value)
                    @if ($value)
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $label }}</p>
                            <p class="mt-1 font-semibold text-slate-900">{{ $value }}</p>
                        </div>
                    @endif
                @endforeach

                @if ($user->kyc_status !== \App\Models\User::KYC_NOT_SUBMITTED)
                    <form method="POST" action="{{ route('admin.kyc.update', $user) }}" class="space-y-3 border-t border-slate-100 pt-4" x-data="{ decision: @js($errors->has('reason') ? 'rejected' : '') }">
                        @csrf
                        @method('PUT')
                        <textarea name="reason" rows="2" x-show="decision === 'rejected'" x-cloak style="display: none" placeholder="Reason for rejection (sent to the customer)" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" />
                        <div class="flex gap-2">
                            <button name="decision" value="approved" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Approve</button>
                            <button type="button" x-show="decision !== 'rejected'" @click="decision = 'rejected'" class="flex-1 rounded-lg border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600">Reject</button>
                            <button name="decision" value="rejected" x-show="decision === 'rejected'" style="display: none" class="flex-1 rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white">Confirm reject</button>
                        </div>
                    </form>
                @endif
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:col-span-2">
                @foreach (['photo' => ['Photo', 'kyc_photo_path'], 'id_front' => ['ID front', 'kyc_id_front_path'], 'id_back' => ['ID back', 'kyc_id_back_path']] as $document => [$label, $column])
                    @if ($user->{$column})
                        @php $url = route('admin.kyc.file', [$user, $document]); @endphp
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $label }}</p>
                                <a href="{{ $url }}" target="_blank" class="text-xs font-semibold text-[color:var(--brand-600)]">Open</a>
                            </div>
                            @if (str_ends_with(strtolower($user->{$column}), '.pdf'))
                                <a href="{{ $url }}" target="_blank" class="flex h-48 items-center justify-center rounded-lg bg-slate-50 text-sm text-slate-500">PDF document — click to open</a>
                            @else
                                <img src="{{ $url }}" alt="{{ $label }}" class="max-h-80 w-full rounded-lg object-contain bg-slate-50">
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
