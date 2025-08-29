<?php
// app/Http/Controllers/system/CarChangeOilsController.php
namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarChangeOilData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarChangeOilsController extends Controller
{
    public function index(Request $request)
    {
        $q       = trim((string) $request->get('q', ''));
        $status  = $request->get('status'); // healthy|due|overdue|null (الكل)

        $carsQuery = Car::with(['lastOilChange', 'driver']);

        // بحث ذكي برقم السيارة / اسم السائق / نوع السيارة
        if ($q !== '') {
            $carsQuery->where(function ($qq) use ($q) {
                $qq->where('number', 'LIKE', "%{$q}%")
                    ->orWhere('type_car', 'LIKE', "%{$q}%")
                    ->orWhereHas('driver', fn($d) => $d->where('name', 'LIKE', "%{$q}%"));
            });
        }

        // فلترة حسب الحالة
        if ($status === 'healthy')   $carsQuery->healthy();
        if ($status === 'due')       $carsQuery->dueSoon();
        if ($status === 'overdue')   $carsQuery->overdue();

        $cars = $carsQuery->orderBy('number')->paginate(12)->withQueryString();

        // إحصائيات عامة (الأرقام السريعة أعلى الصفحة)
        $totalCars   = Car::count();
        $healthyCnt  = Car::healthy()->count();
        $dueCnt      = Car::dueSoon()->count();
        $overdueCnt  = Car::overdue()->count();

        return view('dashboard.car_change_oils.index', compact(
            'cars',
            'q',
            'status',
            'totalCars',
            'healthyCnt',
            'dueCnt',
            'overdueCnt'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'car_id'        => 'required|exists:cars,id',
            'km'            => 'required|integer|min:0',
            'is_oil_change' => 'required|boolean', // true = تغيير زيت جديد، false = مجرد قراءة عدّاد
            'date'          => 'nullable|date',
        ]);

        $car = Car::with('lastOilChange')->findOrFail($data['car_id']);

        // لا تسمح بإدخال قراءة أقل من العداد الحالي
        if (!$data['is_oil_change'] && $data['km'] < $car->odometer) {
            return back()->with('error', 'لا يمكن إدخال قراءة أقل من العداد الحالي.');
        }

        // في تغيير الزيت: يجب أن تكون القراءة أكبر من أو تساوي آخر قراءة مثبتة (إن وجدت)
        if ($data['is_oil_change'] && $car->lastOilChange && $data['km'] < $car->lastOilChange->km_before) {
            return back()->with('error', 'قراءة التغيير لا يجوز أن تكون أقل من آخر قراءة مثبتة لتغيير الزيت.');
        }

        DB::transaction(function () use ($car, $data) {
            if ($data['is_oil_change']) {
                // تثبيت Baseline جديد لدورة زيت
                CarChangeOilData::create([
                    'car_id'    => $car->id,
                    'km_before' => $data['km'],
                    'date'      => $data['date'] ?? now()->toDateString(),
                ]);

                // تحديث عداد السيارة (اختياري، لو نفس القراءة)
                if ($data['km'] > $car->odometer) {
                    $car->update(['odometer' => $data['km']]);
                }
            } else {
                // مجرد تحديث عدّاد السيارة (قراءة يومية/دورية)
                $car->update(['odometer' => $data['km']]);
            }
        });

        return redirect()->route('car_change_oils.index')
            ->with('success', $data['is_oil_change'] ? 'تم تثبيت تغيير الزيت.' : 'تم تحديث قراءة العداد.');
    }
}
