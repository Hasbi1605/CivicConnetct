<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'body',
        'image',
        'citations',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'citations' => 'array',
        ];
    }

    public function hasCitations(): bool
    {
        return !empty($this->citations);
    }

    // ---- Relationships ----

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function endorsements(): HasMany
    {
        return $this->hasMany(Endorsement::class);
    }

    // ---- Scopes ----

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ---- Helpers ----

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isFactCheck(): bool
    {
        return $this->category === 'fact-check';
    }

    public function faktaCount(): int
    {
        return $this->votes()->where('vote', 'fakta')->count();
    }

    public function hoaksCount(): int
    {
        return $this->votes()->where('vote', 'hoaks')->count();
    }

    public function userVote(?int $userId): ?string
    {
        if (!$userId) return null;
        return $this->votes()->where('user_id', $userId)->value('vote');
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function endorsementCount(): int
    {
        return $this->endorsements()->count();
    }

    public function isEndorsedByUser(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->endorsements()->where('user_id', $userId)->exists();
    }

    public function commentCount(): int
    {
        return $this->comments()->count();
    }

    /**
     * Get top-level comments sorted by top count (most topped first).
     */
    public function topComments()
    {
        return $this->comments()
            ->whereNull('parent_id')
            ->withCount('tops')
            ->orderByDesc('tops_count')
            ->orderByDesc('created_at');
    }
}
