<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KycController extends Controller
{
    /** Document keys → user columns. Files live on the private "local" disk. */
    public const DOCUMENTS = [
        'photo' => 'kyc_photo_path',
        'id_front' => 'kyc_id_front_path',
        'id_back' => 'kyc_id_back_path',
    ];

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $isFirstSubmission = ! $user->kyc_photo_path;

        $data = $request->validateWithBag('kyc', [
            'alternate_mobile' => [
                'required', 'string', 'regex:/^\+?[0-9]{7,15}$/',
                Rule::notIn(array_filter([$user->mobile, ltrim((string) $user->mobile, '+')])),
            ],
            'kyc_id_type' => ['required', Rule::in(array_keys(User::KYC_ID_TYPES))],
            'kyc_id_number' => ['required', 'string', 'max:60'],
            'photo' => [$isFirstSubmission ? 'required' : 'nullable', 'image', 'max:4096'],
            'id_front' => [$user->kyc_id_front_path ? 'nullable' : 'required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'id_back' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'alternate_mobile.regex' => 'Enter a valid mobile number (digits only, optional +).',
            'alternate_mobile.not_in' => 'Alternate number must be different from your main number.',
        ]);

        foreach (self::DOCUMENTS as $field => $column) {
            if ($request->hasFile($field)) {
                $old = $user->{$column};
                $user->{$column} = $request->file($field)->store('kyc/'.$user->id, 'local');
                if ($old) {
                    Storage::disk('local')->delete($old);
                }
            }
        }

        $user->forceFill([
            'alternate_mobile' => $data['alternate_mobile'],
            'kyc_id_type' => $data['kyc_id_type'],
            'kyc_id_number' => trim($data['kyc_id_number']),
            // Any change sends KYC back for review.
            'kyc_status' => User::KYC_PENDING,
            'kyc_submitted_at' => now(),
            'kyc_reviewed_at' => null,
            'kyc_reviewed_by' => null,
            'kyc_rejection_reason' => null,
        ])->save();

        return redirect()->to(route('profile.edit').'#kyc')->with('status', 'kyc-submitted');
    }

    public function file(Request $request, string $document)
    {
        return static::serve($request->user(), $document);
    }

    public static function serve(User $user, string $document)
    {
        $column = self::DOCUMENTS[$document] ?? null;
        $path = $column ? $user->{$column} : null;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
