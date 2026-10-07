<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';
    public const ROLE_SHOP = 'shop';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_AUTHORITY = 'authority';

    public const KYC_NOT_SUBMITTED = 'not_submitted';
    public const KYC_PENDING = 'pending';
    public const KYC_APPROVED = 'approved';
    public const KYC_REJECTED = 'rejected';

    public const KYC_ID_TYPES = [
        'citizenship' => 'Citizenship',
        'national_id' => 'National ID',
        'passport' => 'Passport',
        'driving_license' => 'Driving License',
        'voter_id' => 'Voter ID',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'country',
        'country_code',
        'mobile',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'kyc_submitted_at' => 'datetime',
            'kyc_reviewed_at' => 'datetime',
        ];
    }

    /**
     * Submitted KYC (pending review or approved) unlocks transfers and lost reports.
     */
    public function hasKyc(): bool
    {
        return in_array($this->kyc_status, [self::KYC_PENDING, self::KYC_APPROVED], true);
    }

    public function kycStatusLabel(): string
    {
        return match ($this->kyc_status) {
            self::KYC_PENDING => 'Under review',
            self::KYC_APPROVED => 'Verified',
            self::KYC_REJECTED => 'Rejected',
            default => 'Not submitted',
        };
    }

    public function kycReviewer()
    {
        return $this->belongsTo(User::class, 'kyc_reviewed_by');
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'current_owner_id');
    }

    public function transferRequests()
    {
        return $this->hasMany(TransferRequest::class, 'from_user_id');
    }

    public function lostReports()
    {
        return $this->hasMany(LostReport::class, 'reporter_user_id');
    }

    public function userPackages()
    {
        return $this->hasMany(UserPackage::class);
    }

    public function activePackage()
    {
        return $this->hasOne(UserPackage::class)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->latest('starts_at');
    }
}
