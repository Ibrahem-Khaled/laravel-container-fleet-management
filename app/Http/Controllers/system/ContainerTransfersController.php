<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Http\Requests\container\StoreContainerTransferRequest;
use App\Models\Container;
use App\Models\ContainerTransferOrder;
use App\Services\ContainerTransferService;
use Illuminate\Http\Request;

class ContainerTransfersController extends Controller
{
    public function lookup(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $containers = Container::query()
            ->select('id', 'number', 'direction')
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('number', 'like', "%{$q}%")
                        ->orWhere('direction', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        return response()->json(
            $containers->map(fn($c) => [
                'id'   => $c->id,
                'text' => "{$c->number}" . ($c->direction ? " — {$c->direction}" : ''),
            ])
        );
    }

    // 2.2) ملخص أوامر النقل لحاوية (هل لها أوامر؟ إجمالي سابق؟ آخر 5 أوامر)
    public function summary(Container $container)
    {
        // withSum يضيف حقل transfer_orders_sum_price بدون تحميل كل الصفوف
        // أو تقدر تسمي alias مخصص:
        $container->loadSum(['transferOrders as transfer_price_sum'], 'price'); // Laravel relation aggregates
        $container->loadCount('transferOrders');
        $lastOrders = $container->transferOrders()
            ->select('id', 'price', 'note', 'ordered_at')
            ->orderByDesc('ordered_at')->limit(5)->get();

        return response()->json([
            'container_id'        => $container->id,
            'number'              => $container->number,
            'has_orders'          => $container->transfer_orders_count > 0,
            'orders_count'        => (int) $container->transfer_orders_count,
            'transfer_price_sum'  => (float) ($container->transfer_price_sum ?? 0),
            'last_orders'         => $lastOrders,
        ]);
    }

    // 2.3) إنشاء أمر نقل (موجود عندك – أضفته كما هو)
    public function store(StoreContainerTransferRequest $request, ContainerTransferService $service)
    {
        $order = $service->create(
            (int) $request->input('container_id'),
            (float) $request->input('price'),
            $request->input('note')
        );

        return response()->json([
            'message' => 'تم إنشاء أمر النقل للحاوية بنجاح.',
            'order'   => $order,
        ], 201);
    }

    public function destroy(ContainerTransferOrder $order, ContainerTransferService $service)
    {
        $service->delete($order);

        return response()->json([
            'message' => 'تم حذف أمر النقل بنجاح.',
            'deleted_order_id' => $order->id,
        ]);
    }
}
