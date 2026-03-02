<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabSource extends Model
{
    protected $fillable = [
        'lab_room_id',
        'user_id',
        'url',
        'title',
        'summary',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(LabRoom::class, 'lab_room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
