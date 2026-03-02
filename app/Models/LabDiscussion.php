<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabDiscussion extends Model
{
    protected $fillable = [
        'lab_room_id',
        'user_id',
        'claim',
        'evidence',
        'parent_id',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(LabRoom::class, 'lab_room_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(LabDiscussion::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(LabDiscussion::class, 'parent_id');
    }
}
