<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyEndorsement extends Model
{
    protected $fillable = [
        'policy_brief_id',
        'user_id',
    ];

    public function brief(): BelongsTo
    {
        return $this->belongsTo(PolicyBrief::class, 'policy_brief_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
