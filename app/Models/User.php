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
            'mentor' => 'Mentor',
            'agent' => 'Agent',
            'anonim' => 'Pengunjung',
            default => 'Mahasiswa',
        };
    }
}
