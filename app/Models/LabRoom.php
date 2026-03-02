<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LabRoom extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'phase',
        'status',
        'is_private',
        'max_participants',
        'target',
    ];

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
        ];
    }

    // ---- Relationships ----

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lab_participants')->withPivot('joined_at');
    }

    public function sources(): HasMany
    {
        return $this->hasMany(LabSource::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(LabDiscussion::class);
    }

    public function policyBrief(): HasOne
    {
        return $this->hasOne(PolicyBrief::class);
    }

    // ---- Scopes ----

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // ---- Helpers ----

    public function isHost(?int $userId): bool
    {
        return $this->user_id === $userId;
    }

    public function isFull(): bool
    {
        return $this->participants()->count() >= $this->max_participants;
    }

    public function isParticipant(?int $userId): bool
    {
        if (!$userId) return false;
        return $this->participants()->where('user_id', $userId)->exists();
    }

    public function participantCount(): int
    {
        return $this->participants()->count();
    }

    public function phaseLabel(): string
    {
        return match ($this->phase) {
            'literasi' => 'Literasi Kontekstual',
            'analisis' => 'Analisis Kritis',
            'output' => 'Berbasis-data (Output)',
            default => $this->phase,
        };
    }

    public function phaseNumber(): int
    {
        return match ($this->phase) {
            'literasi' => 1,
            'analisis' => 2,
            'output' => 3,
            default => 1,
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Terbuka',
            'in_progress' => 'Berlangsung',
            'completed' => 'Selesai',
            default => $this->status,
        };
    }
}
