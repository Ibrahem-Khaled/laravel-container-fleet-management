<?php
namespace App\Services;

use App\Models\{CustodyAccount, DailyTransaction, CustodyLedgerEntry};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustodyService
{
    /**
     * إنشاء سطر يومية مربوط بعهدة (FK)،
     * مع خيار "مطابقة الدفتر" بإنشاء قيد Ledger مرتبط بـ daily_transaction_id.
     */
    public function createDailyAndMaybeLedger(
        CustodyAccount $account,
        array $dailyData,
        bool $mirrorToLedger = true
    ): DailyTransaction {
        return DB::transaction(function () use ($account, $dailyData, $mirrorToLedger) {
            // فرض الربط بالعهدة
            $dailyData['custody_account_id'] = $account->id;

            // حساب الضريبة من المبلغ الأساسي إذا كانت النسبة أكبر من 0
            $taxPercentage = $dailyData['tax_value'] ?? 0;
            $baseAmount = $dailyData['amount'] ?? 0;
            $taxAmount = $taxPercentage > 0 ? ($baseAmount * $taxPercentage) / 100 : 0;
            $dailyData['total_amount'] = $baseAmount + $taxAmount;

            /** @var DailyTransaction $daily */
            $daily = DailyTransaction::create($dailyData);

            if ($mirrorToLedger) {
                $direction = $daily->type === 'income' ? 'income' : 'expense';

                CustodyLedgerEntry::create([
                    'custody_account_id' => $account->id,
                    'daily_transaction_id' => $daily->id,
                    'direction' => $direction,
                    'amount' => $daily->total_amount,
                    'currency' => $dailyData['currency'] ?? null,
                    'occurred_at' => now(),
                    'created_by' => Auth::id(),
                    'notes' => $daily->notes,
                ]);
            }

            return $daily;
        });
    }

    public function updateDailyAndLedger(DailyTransaction $daily, array $data, bool $syncLedger = true): DailyTransaction
    {
        return DB::transaction(function () use ($daily, $data, $syncLedger) {
            // حساب الضريبة من المبلغ الأساسي إذا كانت النسبة أكبر من 0
            $taxPercentage = $data['tax_value'] ?? $daily->tax_value;
            $baseAmount = $data['amount'] ?? $daily->amount;
            $taxAmount = $taxPercentage > 0 ? ($baseAmount * $taxPercentage) / 100 : 0;
            $data['total_amount'] = $baseAmount + $taxAmount;

            $daily->update($data);

            if ($syncLedger && $daily->custodyAccount && $daily->id) {
                $entry = CustodyLedgerEntry::where('daily_transaction_id', $daily->id)->first();
                if ($entry) {
                    $entry->update([
                        'direction' => $daily->type === 'income' ? 'income' : 'expense',
                        'amount'    => $daily->total_amount,
                        'notes'     => $daily->notes,
                    ]);
                }
            }

            return $daily;
        });
    }

    public function deleteDailyAndLedger(DailyTransaction $daily): void
    {
        DB::transaction(function () use ($daily) {
            CustodyLedgerEntry::where('daily_transaction_id', $daily->id)->delete();
            $daily->delete();
        });
    }
}
