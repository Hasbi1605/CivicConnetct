<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoaxVerdict extends Model
{
    protected $fillable = [
        'hoax_claim_id',
        'user_id',
        'verdict',
        'evidence_url',
        'reasoning',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ── Relationships ──

    public function claim(): BelongsTo
    {
        return $this->belongsTo(HoaxClaim::class, 'hoax_claim_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // ── Helpers ──

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function verdictLabel(): string
    {
        return match ($this->verdict) {
            'valid' => 'Valid',
            'misleading' => 'Menyesatkan',
            'hoax' => 'Hoaks',
            default => $this->verdict,
        };
    }

    public function verdictColor(): string
    {
        return match ($this->verdict) {
            'valid' => '#10b981',
            'misleading' => '#f59e0b',
            'hoax' => '#ef4444',
            default => '#64748b',
        };
    }
}
