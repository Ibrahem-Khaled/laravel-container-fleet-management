<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

final class CustodyAccount extends BaseModel
{
    protected $fillable = [
        'user_id',
        'opening_balance',
        'status',
        'opened_by',
        'closed_by',
        'opened_at',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'opened_at'       => 'datetime',
        'closed_at'       => 'datetime',
        'opening_balance' => 'decimal:2',
    ]; // كاستينج رسمي للديت/ديسمل. :contentReference[oaicite:3]{index=3}

    /* ===================== العلاقات ===================== */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(CustodyLedgerEntry::class);
    }

    // اليومية المرتبطة مباشرةً بهذه العهدة (FK مباشر كما اخترت)
    public function dailyTransactions(): HasMany
    {
        return $this->hasMany(DailyTransaction::class, 'custody_account_id');
    }

    /* ===================== Scopes مفيدة ===================== */

    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForRole($query, int $roleId)
    {
        return $query->whereHas('owner', fn($uq) => $uq->where('role_id', $roleId));
    }

    public function scopeSearch($query, string $term)
    {
        $like = "%{$term}%";
        return $query->whereHas('owner', function ($uq) use ($like) {
            $uq->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('phone', 'like', $like);
        });
    }

    /* ===================== رصيد جارٍ ===================== */

    public function currentBalance(): float
    {
        $inc = (float) $this->entries()
            ->whereIn('direction', ['issue', 'income', 'transfer_in', 'adjustment'])
            ->sum('amount');

        $dec = (float) $this->entries()
            ->whereIn('direction', ['return', 'expense', 'transfer_out'])
            ->sum('amount');

        return (float) $this->opening_balance + $inc - $dec;
    }
}
