<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HoaxClaim extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'source_url',
        'source_platform',
        'category',
        'status',
        'final_verdict',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // ── Relationships ──

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verdicts(): HasMany
    {
        return $this->hasMany(HoaxVerdict::class);
    }

    public function approvedVerdicts(): HasMany
    {
        return $this->hasMany(HoaxVerdict::class)->where('status', 'approved');
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    // ── Helpers ──

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Get counts of each approved verdict type.
     */
    public function verdictCounts(): array
    {
        $verdicts = $this->approvedVerdicts;

        return [
            'valid' => $verdicts->where('verdict', 'valid')->count(),
            'misleading' => $verdicts->where('verdict', 'misleading')->count(),
            'hoax' => $verdicts->where('verdict', 'hoax')->count(),
            'total' => $verdicts->count(),
        ];
    }

    /**
     * Get the dominant verdict and its percentage.
     */
    public function consensusResult(): array
    {
        $counts = $this->verdictCounts();
        $total = $counts['total'];

        if ($total === 0) {
            return ['verdict' => null, 'percentage' => 0, 'total' => 0];
        }

        $max = max($counts['valid'], $counts['misleading'], $counts['hoax']);

        if ($max === $counts['hoax']) {
            $verdict = 'hoax';
        } elseif ($max === $counts['misleading']) {
            $verdict = 'misleading';
        } else {
            $verdict = 'valid';
        }

        return [
            'verdict' => $verdict,
            'percentage' => round(($max / $total) * 100),
            'total' => $total,
        ];
    }

    /**
     * Check if auto-resolve threshold is met and resolve if so.
     * Threshold: ≥10 approved verdicts with >75% consensus.
     */
    public function checkAutoResolve(): bool
    {
        if ($this->isResolved()) {
            return false;
        }

        $counts = $this->verdictCounts();
        $total = $counts['total'];

        if ($total < 10) {
            return false;
        }

        $consensus = $this->consensusResult();

        if ($consensus['percentage'] > 75) {
            $this->update([
                'status' => 'resolved',
                'final_verdict' => $consensus['verdict'],
                'resolved_at' => now(),
            ]);
            return true;
        }

        return false;
    }

    /**
     * Check if a user has already submitted a verdict.
     */
    public function hasVerdictFrom(int $userId): bool
    {
        return $this->verdicts()->where('user_id', $userId)->exists();
    }

    /**
     * Get the user's verdict for this claim.
     */
    public function userVerdict(int $userId): ?HoaxVerdict
    {
        return $this->verdicts()->where('user_id', $userId)->first();
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            'politik' => 'Politik',
            'kesehatan' => 'Kesehatan',
            'teknologi' => 'Teknologi',
            'sosial' => 'Sosial',
            default => 'Lainnya',
        };
    }

    public function platformLabel(): string
    {
        return match ($this->source_platform) {
            'twitter' => 'Twitter/X',
            'whatsapp' => 'WhatsApp',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'website' => 'Website/Blog',
            default => 'Lainnya',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Peninjauan',
            'open' => 'Terbuka untuk Verifikasi',
            'resolved' => 'Selesai',
            default => $this->status,
        };
    }

    public function finalVerdictLabel(): string
    {
        return match ($this->final_verdict) {
            'valid' => 'Valid',
            'misleading' => 'Menyesatkan',
            'hoax' => 'Hoaks',
            default => '-',
        };
    }
}
