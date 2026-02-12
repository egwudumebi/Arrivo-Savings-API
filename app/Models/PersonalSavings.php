<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalSavings extends Model
{
    protected $table = 'personal_savings';

    protected $fillable = [
        'user_id',
        'name',
        'balance',
        'target_amount',
        'currency',
        'status',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'target_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
