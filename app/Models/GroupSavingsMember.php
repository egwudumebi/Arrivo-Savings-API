<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupSavingsMember extends Model
{
    protected $table = 'group_savings_members';

    protected $fillable = [
        'group_savings_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(GroupSavings::class, 'group_savings_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
