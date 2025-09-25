<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContainerFlowChangeRequest;
use App\Models\Container;
use App\Models\Tip;
use App\Models\User;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContainerFlowController extends Controller
{
    public function index(Request $request)
    {
        $statuses = ['wait', 'transport', 'done', 'rent', 'storage'];
        $statusMap = [
            'wait' => 'المواعيد',
            'transport' => 'المحملة',
            'rent' => 'مؤجرة',
            'storage' => 'تخزين',
            'done' => 'الفارغ',
        ];

        // الافتراضي يفتح على "المحمّلة" (نقل)
        $selectedStatus = $request->get('status', 'wait');
        $search         = $request->get('search');

        $q = Container::query()
            ->with([
                'customs:id,client_id,clearance_office_id,statement_number',
                'customs.client:id,name',
                'customs.clearanceOffice:id,name',
            ])
            ->latest();

        if ($selectedStatus !== 'all' && in_array($selectedStatus, $statuses)) {
            $q->where('status', $selectedStatus);
        }

        if ($search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('number', 'like', "%{$search}%")
                    ->orWhere('direction', 'like', "%{$search}%")
                    ->orWhereHas('customs', fn($c) => $c->where('statement_number', 'like', "%{$search}%"))
                    ->orWhereHas('customs.client', fn($c) => $c->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('customs.clearanceOffice', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $containers = $q->paginate(12)->withQueryString();

        // عدّاد لكل حالة (يُستخدم في الكروت والتبويبات)
        $stats = Container::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')->pluck('c', 'status');

        $drivers = User::WithRoleNames(['driver'])->orderBy('name')->get(['id', 'name']);
        $cars    = Car::orderBy('id', 'desc')->get(['id', 'number']);

        return view('dashboard.containers.flow', compact(
            'containers',
            'statuses',
            'statusMap',
            'selectedStatus',
            'search',
            'stats',
            'drivers',
            'cars'
        ));
    }

    public function change(\App\Http\Requests\ContainerFlowChangeRequest $request, \App\Models\Container $container)
    {
        $from = $container->status;
        $to   = $request->new_status;

        DB::transaction(function () use ($request, $container, $from, $to) {
            // لو رجعنا خطوة (إلى "انتظار") نحذف آخر Tip للحالة السابقة وأوامر النقل
            if ($to === 'wait' && in_array($from, ['transport', 'storage', 'done', 'rent'])) {
                // حذف آخر Tip للحالة السابقة
                $lastTip = \App\Models\Tip::where('container_id', $container->id)
                    ->where('type', $from)->latest('id')->first();
                if ($lastTip) {
                    $lastTip->delete();
                }

                // حذف جميع أوامر النقل المرتبطة بالحاوية
                \App\Models\ContainerTransferOrder::where('container_id', $container->id)->delete();
            }

            // تحديث حالة الحاوية
            $container->update(['status' => $to]);

            // إنشاء Tip لكل الحالات عدا "انتظار"
            if ($to !== 'wait') {
                \App\Models\Tip::create([
                    'container_id' => $container->id,
                    'driver_id'    => $request->driver_id,
                    'car_id'       => $request->car_id,
                    'price'        => $request->price ?? 20,
                    'type'         => $to,
                ]);
            }
        });

        $message = 'تم تنفيذ العملية بنجاح.';
        if ($to === 'wait' && in_array($from, ['transport', 'storage', 'done', 'rent'])) {
            $message = 'تم إرجاع الحاوية إلى حالة الانتظار وإلغاء جميع أوامر النقل المرتبطة بها.';
        }

        return back()->with('success', $message);
    }

    // AJAX: سيارات سائق
    public function carsByDriver(User $driver)
    {
        $cars = Car::where('driver_id', $driver->id)->orderBy('id', 'desc')->get(['id', 'number']);
        return response()->json(['cars' => $cars]);
    }
}
