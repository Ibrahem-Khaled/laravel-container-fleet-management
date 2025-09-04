<?php

namespace App\Services;

use App\Models\Container;
use App\Models\ContainerTransferOrder;
use App\Models\DailyTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContainerTransferService
{
    /**
     * إنشاء أمر نقل، وترحيل/تجميع المنصرف على مستوى الحاوية (بدون قيد اليوم)،
     * ودمج الملاحظات القديمة مع الجديدة بعلامة فاصلة.
     */
    public function create(
        int $containerId,
        float $price,
        ?string $note = null,
        string $method = 'cash'   // cash | bank
    ): ContainerTransferOrder {
        if ($price < 0) {
            throw ValidationException::withMessages(['price' => 'السعر يجب أن يكون رقمًا موجبًا.']);
        }

        return DB::transaction(function () use ($containerId, $price, $note, $method) {

            // 1) الحاوية
            /** @var \App\Models\Container|null $container */
            $container = Container::query()->find($containerId);
            if (! $container) {
                throw ValidationException::withMessages(['container_id' => 'الحاوية غير موجودة.']);
            }

            // 2) إنشاء أمر النقل نفسه
            $order = $container->transferOrders()->create([
                'price'      => $price,
                'note'       => $note,
                'ordered_at' => now(),
            ]);

            // 3) اجلب سجل المنصرف المُجمّع للحاوية (بدون فلترة تاريخ)
            //    واقفله lockForUpdate لمنع تضارب التحديث المتوازي
            $daily = DailyTransaction::query()
                ->where('transactionable_type', $container->getMorphClass()) // أو App\Models\Container
                ->where('transactionable_id', $container->getKey())
                ->where('type', 'expense')
                ->lockForUpdate()
                ->first();

            // 4) حضّر نص الملاحظات المُدمج
            $stamp   = now()->format('Y-m-d H:i');
            $delim   = "\n———\n"; // فاصل واضح بين الملاحظات
            $newLine = $note ? "[+ {$stamp}] {$note}" : null;

            if ($daily) {
                // زيادة تراكمية
                $daily->amount       = (float) $daily->amount + $price;
                $daily->total_amount = (float) $daily->total_amount + $price;

                // دمج الملاحظة الجديدة (إن وُجدت) مع القديمة
                if ($newLine) {
                    $daily->notes = trim(
                        ($daily->notes ? $daily->notes . $delim : '')
                            . $newLine
                    );
                }

                // (اختياري) تحديث طريقة الدفع لآخر عملية
                $daily->method = $method;

                $daily->save();
            } else {
                // أول منصرف للحاوية (بدون يوم)
                DailyTransaction::create([
                    'transactionable_type' => $container->getMorphClass(),
                    'transactionable_id'   => $container->getKey(),
                    'type'                 => 'expense',
                    'method'               => $method,
                    'amount'               => $price,
                    'tax_value'            => 0,
                    'total_amount'         => $price,
                    'notes'                => $newLine, // تبدأ بأول نوتة مع الختم الزمني
                    // created_at / updated_at تتضبط تلقائيًا
                ]);
            }

            return $order->fresh();
        });
    }

    public function delete(ContainerTransferOrder $order): void
    {
        DB::transaction(function () use ($order) {

            $container = $order->container()->firstOrFail();
            $price = (float) $order->price;

            // اقفل سجل المنصرف المُجمّع للحاوية (لو موجود)
            $daily = DailyTransaction::query()
                ->where('transactionable_type', $container->getMorphClass())
                ->where('transactionable_id', $container->getKey())
                ->where('type', 'expense')
                ->lockForUpdate()
                ->first();

            if ($daily) {
                // اطرح قيمة الأمر
                $daily->amount       = max(0, (float)$daily->amount - $price);
                $daily->total_amount = max(0, (float)$daily->total_amount - $price);

                // نضيف سطر تأريخي بسيط يفيد بعملية الحذف
                $stamp = now()->format('Y-m-d H:i');
                $delim = "\n———\n";
                $line  = "[- {$stamp}] حذف أمر نقل #{$order->id} بمبلغ " . number_format($price, 2);

                $daily->notes = trim(($daily->notes ? $daily->notes . $delim : '') . $line);

                // (اختياري) لو عايز تمسح السجل لو بقى صفر
                // if ($daily->amount <= 0 && $daily->total_amount <= 0) {
                //     $daily->delete();
                // } else {
                $daily->save();
                // }
            }

            // احذف الأمر نفسه (حذف دائم — أو فعِّل SoftDeletes على الموديل لو عايز)
            $order->delete();
        });
    }
}
