<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupSavings extends Model
{
    protected $table = 'group_savings';

    protected $fillable = [
        'creator_id',
        'name',
        'description',
        'balance',
        'target_amount',
        'currency',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'target_amount' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupSavingsMember::class, 'group_savings_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'group_savings_id');
    }
}
