<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PolicyBrief extends Model
{
    protected $fillable = [
        'user_id',
        'lab_room_id',
        'title',
        'summary',
        'problem',
        'analysis',
        'recommendation',
        'template_type',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    // ---- Relationships ----

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function labRoom(): BelongsTo
    {
        return $this->belongsTo(LabRoom::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function endorsements(): HasMany
    {
        return $this->hasMany(PolicyEndorsement::class);
    }

    // ---- Scopes ----

    public function scopePublished($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ---- Helpers ----

    public function isEndorsedBy(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->endorsements()->where('user_id', $userId)->exists();
    }

    public function endorsementCount(): int
    {
        return $this->endorsements()->count();
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

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

    public function templateLabel(): string
    {
        return match ($this->template_type) {
            'standar' => 'Policy Brief Standar',
            'data-driven' => 'Data-Driven Brief',
            'quick-response' => 'Quick Response',
            default => $this->template_type,
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'draft' => 'Draft',
            'pending' => 'Menunggu Review',
            'approved' => 'Dipublikasi',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }
}
