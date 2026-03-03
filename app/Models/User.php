<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'jurusan',
        'universitas',
        'bio',
        'role',
        'avatar',
        'is_profile_complete',
        'identity_card_type',
        'identity_card_image',
        'nim_nidn',
        'identity_status',
        'identity_rejection_reason',
        'identity_verified_at',
        'identity_verified_by',
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
            'is_profile_complete' => 'boolean',
            'identity_verified_at' => 'datetime',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function labRooms(): HasMany
    {
        return $this->hasMany(LabRoom::class);
    }

    public function joinedRooms(): BelongsToMany
    {
        return $this->belongsToMany(LabRoom::class, 'lab_participants')->withPivot('joined_at');
    }

    public function policyBriefs(): HasMany
    {
        return $this->hasMany(PolicyBrief::class);
    }

    public function hoaxClaims(): HasMany
    {
        return $this->hasMany(HoaxClaim::class);
    }

    public function hoaxVerdicts(): HasMany
    {
        return $this->hasMany(HoaxVerdict::class);
    }

    /**
     * Check if user is a CIVIC Agent (can moderate posts).
     */
    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    /**
     * Check if user is anonymous (public visitor).
     */
    public function isAnonim(): bool
    {
        return $this->role === 'anonim';
    }

    /**
     * Check if user's identity has been verified (KYA approved).
     */
    public function isIdentityVerified(): bool
    {
        return $this->identity_status === 'approved';
    }

    /**
     * Check if user's identity verification is pending.
     */
    public function isIdentityPending(): bool
    {
        return $this->identity_status === 'pending';
    }

    /**
     * Check if user's identity verification was rejected.
     */
    public function isIdentityRejected(): bool
    {
        return $this->identity_status === 'rejected';
    }

    /**
     * Check if user has not yet submitted identity verification.
     */
    public function isIdentityUnsubmitted(): bool
    {
        return $this->identity_status === 'unsubmitted' || $this->identity_status === null;
    }

    /**
     * The CIVIC Agent who verified this user's identity.
     */
    public function identityVerifier()
    {
        return $this->belongsTo(User::class, 'identity_verified_by');
    }

    /**
     * Get the identity card type label.
     */
    public function getIdentityCardLabelAttribute(): string
    {
        return match ($this->identity_card_type) {
            'ktm' => 'Kartu Tanda Mahasiswa',
            'ktd' => 'Kartu Tanda Dosen',
            default => '-',
        };
    }

    /**
     * Get unread notifications count.
     */
    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            if (str_starts_with($this->avatar, 'http')) {
                return $this->avatar;
            }
            if (str_starts_with($this->avatar, '/')) {
                return asset($this->avatar);
            }
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff';
    }

    /**
     * Get display string for role badge.
     */
    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'mentor' => 'Dosen',
            'agent' => 'Agent',
            'anonim' => 'Pengunjung',
            default => 'Mahasiswa',
        };
    }

    /**
     * Get the identity verification status badge.
     */
    public function getIdentityStatusBadgeAttribute(): string
    {
        return match ($this->identity_status) {
            'pending' => 'Menunggu Verifikasi',
            'approved' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default => 'Belum Diverifikasi',
        };
    }
}
