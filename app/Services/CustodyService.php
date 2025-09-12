<?php

namespace App\Services;

use App\Models\{CustodyAccount, CustodyLedgerEntry, CashCount, User};
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CustodyService
{
    public function issue(int $userId, float $amount, int $adminId, ?string $notes = null): CustodyLedgerEntry
    {
        return DB::transaction(function () use ($userId, $amount, $adminId, $notes) {
            $account = CustodyAccount::firstOrCreate(
                ['user_id' => $userId, 'status' => 'open'],
                ['opening_balance' => 0, 'opened_by' => $adminId, 'opened_at' => now()]
            );
            return $account->ledgerEntries()->create([
                'direction' => 'issue',
                'amount' => $amount,
                'occurred_at' => now(),
                'created_by' => $adminId,
                'notes' => $notes,
            ]);
        });
    }

    public function entry(CustodyAccount $account, array $data, int $actorId): CustodyLedgerEntry
    {
        return DB::transaction(function () use ($account, $data, $actorId) {
            $payload = array_merge($data, [
                'occurred_at' => $data['occurred_at'] ?? now(),
                'created_by'  => $actorId,
            ]);
            return $account->ledgerEntries()->create($payload);
        });
    }

    public function transfer(CustodyAccount $from, CustodyAccount $to, float $amount, int $actorId, ?string $notes = null): void
    {
        if ($from->id === $to->id) throw new InvalidArgumentException('Cannot transfer to same account');
        DB::transaction(function () use ($from, $to, $amount, $actorId, $notes) {
            $from->ledgerEntries()->create([
                'direction' => 'transfer_out',
                'amount' => $amount,
                'occurred_at' => now(),
                'created_by' => $actorId,
                'counterparty_user_id' => $to->user_id,
                'notes' => $notes,
            ]);
            $to->ledgerEntries()->create([
                'direction' => 'transfer_in',
                'amount' => $amount,
                'occurred_at' => now(),
                'created_by' => $actorId,
                'counterparty_user_id' => $from->user_id,
                'notes' => $notes,
            ]);
        });
    }

    public function countAndPost(CustodyAccount $account, float $expected, float $counted, int $actorId, ?string $notes = null): CashCount
    {
        return DB::transaction(function () use ($account, $expected, $counted, $actorId, $notes) {
            $diff = $counted - $expected;
            $count = $account->cashCounts()->create([
                'counted_by' => $actorId,
                'total_expected' => $expected,
                'total_counted' => $counted,
                'difference' => $diff,
                'status' => 'posted',
                'notes' => $notes,
            ]);
            if ($diff != 0.0) {
                $account->ledgerEntries()->create([
                    'direction' => 'adjustment',
                    'amount' => abs($diff),
                    'occurred_at' => now(),
                    'created_by' => $actorId,
                    'notes' => $diff > 0 ? 'زيادة جرد' : 'عجز جرد',
                ]);
            }
            return $count;
        });
    }
}
