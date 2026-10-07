<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\KycController as UserKycController;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KycController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', User::KYC_PENDING);

        $users = User::query()
            ->when($status !== 'all', fn ($q) => $q->where('kyc_status', $status))
            ->when($status === 'all', fn ($q) => $q->where('kyc_status', '!=', User::KYC_NOT_SUBMITTED))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->input('search');
                $q->where(fn ($inner) => $inner
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('alternate_mobile', 'like', "%{$search}%")
                    ->orWhere('kyc_id_number', 'like', "%{$search}%"));
            })
            ->orderByDesc('kyc_submitted_at')
            ->paginate(25)
            ->withQueryString();

        $counts = User::selectRaw('kyc_status, count(*) as total')
            ->groupBy('kyc_status')
            ->pluck('total', 'kyc_status');

        return view('admin.kyc.index', compact('users', 'status', 'counts'));
    }

    public function show(User $user)
    {
        $user->load('kycReviewer');

        return view('admin.kyc.show', compact('user'));
    }

    public function update(Request $request, User $user, NotificationService $notifier)
    {
        abort_if($user->kyc_status === User::KYC_NOT_SUBMITTED, 404);

        $data = $request->validate([
            'decision' => ['required', Rule::in([User::KYC_APPROVED, User::KYC_REJECTED])],
            'reason' => ['required_if:decision,'.User::KYC_REJECTED, 'nullable', 'string', 'max:255'],
        ]);

        $user->forceFill([
            'kyc_status' => $data['decision'],
            'kyc_reviewed_at' => now(),
            'kyc_reviewed_by' => $request->user()->id,
            'kyc_rejection_reason' => $data['decision'] === User::KYC_REJECTED ? $data['reason'] : null,
        ])->save();

        $message = $data['decision'] === User::KYC_APPROVED
            ? 'Your KYC has been verified.'
            : 'Your KYC was rejected: '.$data['reason'].'. Please update it from My Profile.';
        $notifier->sendEmail($user->email, 'KYC '.($data['decision'] === User::KYC_APPROVED ? 'verified' : 'rejected'), $message);
        $notifier->sendSms($user->mobile, $message);

        return redirect()->route('admin.kyc.index')->with('status', 'KYC '.$data['decision'].' for '.$user->name.'.');
    }

    public function file(User $user, string $document)
    {
        return UserKycController::serve($user, $document);
    }
}
